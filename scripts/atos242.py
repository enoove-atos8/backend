from deploy_atos242 import DeployAtos242
import sys



obj = DeployAtos242()

args = sys.argv
params = []
finalParams = None

if len(args) >= 5:

    if args[1] == 'deploy':

        for idx, arg in enumerate(args):

            if idx != 0:

                if '=' in arg:

                    if arg.split('=')[0] == '-r':
                        repository = arg.split('=')[1]  # enoove/atos242-backend
                        params.append(repository)
                        continue

                    elif arg.split('=')[0] == '-tag':
                        image_tag = arg.split('=')[1]  # 00.00.029
                        params.append(image_tag)
                        continue

                    elif arg.split('=')[0] == '-type':
                        type = arg.split('=')[1]  # build or all
                        params.append(type)
                        continue

                    elif arg.split('=')[0] == '-env':
                        env = arg.split('=')[1]  # dev
                        params.append(env)
                        continue

                    else:
                        print(
                            'Invalid command, do not was informed the tag or repository parameter (-r=user/image or -t=00.00.000)')

        finalParams = obj.run_docker_commands(params)

else:
    print('Invalid command, there are some parameters that do not was informed')



# ======= Upload docker-compose.yml =========

obj.upload_docker_compose(finalParams)

# ======= Clean docker environment and start containers =========

obj.clean_docker_images(finalParams)

# ======= Clean docker environment and start containers =========

obj.start_docker_containers(finalParams)
