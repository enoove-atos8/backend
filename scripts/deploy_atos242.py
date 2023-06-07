from dotenv import load_dotenv
import getopt
import os
from time import sleep
from paramiko import AutoAddPolicy, SSHClient
import sys
import subprocess


class DeployAtos242:
    

    def run_docker_commands(self, params):

        cmd_docker_build = ''
        cmd_docker_push = ''
        dict_params = {
            'repository': self.verify_index(params, 0),
            'image_tag': self.verify_index(params, 1),
            'type': self.verify_index(params, 2),
            'env': self.verify_index(params, 3),
        }

        if dict_params['type'] == 'build':
            sudo = 'sudo ' if dict_params['sudo'] == 'yes' else ''
            dockerfile = 'DockerfileDev' if dict_params['env'] == 'dev' else 'DockerfileProd'

            cmd_docker_build = sudo + 'docker build -t ' + dict_params['repository'] + ':' + dict_params[
                'image_tag'] + ' -f ' + dockerfile + ' .'

        if dict_params['type'] == 'all':
            dockerfile = 'DockerfileDev' if dict_params['env'] == 'dev' else 'DockerfileProd'

            cmd_docker_build = 'docker build -t ' + dict_params['repository'] + ':' + dict_params[
                'image_tag'] + ' -f ' + dockerfile + ' .'
            cmd_docker_push = 'docker push ' + dict_params['repository'] + ':' + dict_params['image_tag']

        print(' ')
        print('Building and Push docker image...')

        returned_value = subprocess.call(cmd_docker_build + ' && ' + cmd_docker_push, shell=True)

        print('Build and Push docker image ' + dict_params['repository'] + ':' + dict_params['image_tag'])

        return dict_params

    def verify_index(self, dict, index):
        try:
            return dict[index]

        except IndexError:
            return None

    def connect_ssh(self):

        hostname = 'ec2-3-14-69-129.us-east-2.compute.amazonaws.com'
        username = 'ubuntu'
        password = ''

        ssh = SSHClient()
        ssh.set_missing_host_key_policy(AutoAddPolicy())
        ssh.connect(hostname=hostname, username=username, key_filename='scripts/test_atos242.pem')

        return ssh

    def upload_docker_compose(self, finalParams):

        sleep(1)
        print(' ')
        print('Upload docker compose...')

        ssh = self.connect_ssh()

        sftp = ssh.open_sftp()
        sftp.put('scripts/docker-compose.yml', 'docker-compose.yml')
        print('docker-compose file updated!')
        sftp.close()

    def update_version_env(self, version):
        ...

    def clean_docker_images(self, finalParams):

        sleep(1)
        print(' ')
        print('Clean docker images...')

        ssh = self.connect_ssh()

        stdin, stdout, stderr = ssh.exec_command(
            'sudo docker compose stop && sudo docker system prune -f && sudo docker image prune -f -a')
        exit_status = stdout.channel.recv_exit_status()

        print('Containers stopped and images deleted') if exit_status == 0 else print("Error", exit_status)

    def start_docker_containers(self, finalParams):

        sleep(1)
        print(' ')
        print('Start docker containers...')        

        if (finalParams['env'] == 'dev'):
            cmd = 'sudo docker compose up -d && sudo docker exec backend nginx && sudo docker exec backend cp .env.dev .env && sudo docker exec backend php artisan config:cache && sudo docker exec backend php artisan route:clear'
        elif (finalParams['env'] == 'test'):
            cmd = 'sudo docker compose up -d && sudo docker exec backend nginx && sudo docker exec backend cp .env.test .env && sudo docker exec backend php artisan config:cache && sudo docker exec backend php artisan route:clear'
        elif (finalParams['env'] == 'hml'):
            cmd = 'sudo docker compose up -d && sudo docker exec backend nginx && sudo docker exec backend cp .env.hml .env && sudo docker exec backend php artisan config:cache && sudo docker exec backend php artisan route:clear'
        elif (finalParams['env'] == 'prod'):
            cmd = 'sudo docker compose up -d && sudo docker exec backend nginx && sudo docker exec backend cp .env.prod .env && sudo docker exec backend php artisan config:cache && sudo docker exec backend php artisan route:clear'

        ssh = self.connect_ssh()

        stdin, stdout, stderr = ssh.exec_command(cmd)
        exit_status = stdout.channel.recv_exit_status()

        print('Containers started') if exit_status == 0 else print("Error", exit_status)
