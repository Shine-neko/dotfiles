build:
	docker build -t shineneko/php -f docker/Dockerfile.php docker
	docker build -t fabric -f docker/Dockerfile.Fabric docker
	docker build -t ansible -f docker/Dockerfile.Ansible docker
