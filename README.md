# Docker setup


kubectl -n airs-66-staging-9ayqw6 port-forward service/airs-mysql-staging 3306:3306
kubectl -n airs-66-staging-9ayqw6 exec -it $(kubectl get pods -n airs-66-staging-9ayqw6 | grep airs-staging | cut -d" " -f 1) -- sh 

kubectl -n airs-66-staging-9ayqw6 exec -it $(kubectl get pods -n airs-66-staging-9ayqw6 | grep airs-mysql-staging- | cut -d" " -f 1) -- sh 


### Create Docker/mysql/data && Docker/mysql/log directories
`mkdir -p  Docker/mysql/data Docker/mysql/log`

### Create docker containers
`./build-and-run.sh`

### Unzip database 
`unzip Docker/Mysql/initialdb.sql.zip`

### Drop DB
`docker exec -i <DOCKER_INSTANCE> mysql -u airs-dev -h 127.0.0.1 -P3306 -pairs-dev-php-pwd -e "drop database airs_dev_db"`

### Create DB
`docker exec -i <DOCKER_INSTANCE> mysql -u airs-dev -h 127.0.0.1 -P3306 -pairs-dev-php-pwd -e "create database airs_dev_db"`

### Import database
`docker exec -i <DOCKER_INSTANCE> mysql -u airs-dev -h 127.0.0.1 -P3306 -pairs-dev-php-pwd airs_dev_db < ./airs_demo.sql`

---

# AIRS 
### eerste commit vanuit CVS, 31-7-2020

Ontwikkelaars
* Robert van Versendaal
* Chris van Santen
* Ricardo Monsees


Codebase
* PHP > 5.2
* Mysql > 5.0
* zendencoder > 3.3

