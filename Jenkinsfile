pipeline {

  environment {
    dockerapp = "udecdj2022/app"
    dockeradmin = "udecdj2022/phpmyadmin"
    dockerimageapp = ""
    dockerimagemyadmin = ""
  }

  agent any
  

  stages {

    stage('Checkout Code') {
      steps {
        git credentialsId: 'githubhernan', url: 'https://github.com/udecdj2022/lovedeops-haep.git', branch:'main'
      }
    }

     stage('SonarQube analysis') {
      steps {
        sh "cd /var/jenkins_home/workspace/lovedevops-haep/"
        // Run the SonarQube Scanner container inside the Jenkins container and send the result to the server
        withCredentials([string(credentialsId: 'sonarqubeGlobal', variable: 'SONAR_TOKEN')]) {
          sh 'docker run --rm \
            --network host \
            -v $(pwd):/usr/src \
            sonarsource/sonar-scanner-cli \
            -Dsonar.host.url=http://scanner.ucol.mx:9000 \
            -Dsonar.login=sqa_81e6208efcb88891bc709a7dfc94d303c91b4f87 \
            -Dsonar.projectKey=app \
            -Dsonar.sources=app \
            -Dsonar.projectName=app \
            -Dsonar.projectVersion=1.0 \
            -Dsonar.projectDescription=app \
            -Dsonar.language=php \
            -Dsonar.php.coverage.reportPaths=coverage.xml \
	    -Dsonar.scm.provider=git \
            -Dsonar.php.tests.reportPath=phpunit.xml'
        }
      }
    }
  


    stage('Build image APP') {
      steps{
        dir('app'){
        script {
          dockerimageapp = docker.build dockerapp
        }
      }
    }
  }

    stage('Pushing Image APP') {
      environment {
               registryCredential = 'dockerhubhaep'
           }
      steps{
        dir('app'){
        script {
          docker.withRegistry( 'https://registry.hub.docker.com', registryCredential ) {
            dockerimageapp.push("vpipeline")
          }
        }
      }
    }
  }

       stage('Build image MYADMIN') {
      steps{
        dir('phpmyadmin'){
        script {
          dockerimagemyadmin = docker.build dockeradmin
        }
      }
    }
  }

    stage('Pushing Image MYADMIN') {
      environment {
               registryCredential = 'dockerhubhaep'
           }
      steps{
        dir('phpmyadmin'){
        script {
          docker.withRegistry( 'https://registry.hub.docker.com', registryCredential ) {
            dockerimagemyadmin.push("vpipeline")
          }
        }
      }
    }
  }

//   stage('Static Code Analysis') {
//   steps {
//      dir('app'){
//      withSonarQubeEnv('scanner') {
//       sh 'mvn sonar:sonar -Dsonar.language=php'
//        }
//      }
//    }
//  }

   stage('APLICANDO DEPLOYMENTS APP'){
   steps{
    sshagent(['sshsanchez'])
    {
     sh 'cd app && scp -r -o StrictHostKeyChecking=no deployment-haep.yaml digesetuser@148.213.1.131:/home/digesetuser/'
      script{
        try{
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl apply -f  deployment-haep.yaml --kubeconfig=/home/digesetuser/.kube/config'
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout restart deployment app -n haep --kubeconfig=/home/digesetuser/.kube/config'
//           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout status deployment app -n haep --kubeconfig=/home/digesetuser/.kube/config'
          }catch(error)
       {}
     }
    }
  }
 }

     stage('APLICANDO DEPLOYMENTS MYSQL'){
   steps{
    sshagent(['sshsanchez'])
    {
     sh 'cd mysql && scp -r -o StrictHostKeyChecking=no deployment-mysql-haep8.yaml digesetuser@148.213.1.131:/home/digesetuser/'
      script{
        try{
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl apply -f  deployment-mysql-haep8.yaml --kubeconfig=/home/digesetuser/.kube/config'
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout restart deployment mysql-deployment -n haep --kubeconfig=/home/digesetuser/.kube/config'
//           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout status deployment mysql-deployment -n haep --kubeconfig=/home/digesetuser/.kube/config'
          }catch(error)
       {}
     }
    }
  }
 }

         
   stage('APLICANDO DEPLOYMENTS MYADMIN'){
   steps{
    sshagent(['sshsanchez'])
    {
     sh 'cd phpmyadmin && scp -r -o StrictHostKeyChecking=no deployment-phpmyadmin8.yaml digesetuser@148.213.1.131:/home/digesetuser/'
      script{
        try{
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl apply -f  deployment-phpmyadmin8.yaml --kubeconfig=/home/digesetuser/.kube/config'
           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout restart deployment phpmyadmin-deployment -n haep --kubeconfig=/home/digesetuser/.kube/config'
//           sh 'ssh digesetuser@148.213.1.131 microk8s.kubectl rollout status deployment phpmyadmin-deployment -n haep --kubeconfig=/home/digesetuser/.kube/config'
          }catch(error)
       {}
     }
    }
  }
 }
}


  post{
            success{
            slackSend channel: 'prueba_pipeline_haep', color: 'good', failOnError: true, message: "${custom_msg()}", teamDomain: 'universidadde-bea3869', tokenCredentialId: 'slackpass' }
      }
   }


  def custom_msg()
  {
  def JENKINS_URL= "jarvis.ucol.mx:8080"
  def JOB_NAME = env.JOB_NAME
  def BUILD_ID= env.BUILD_ID
  def JENKINS_LOG= " DEPLOY LOG: Job [${env.JOB_NAME}] Logs path: ${JENKINS_URL}/job/${JOB_NAME}/${BUILD_ID}/consoleText"
  return JENKINS_LOG
  }

