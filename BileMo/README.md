# JuncaHugo_7_16122022

[![Maintainability](https://api.codeclimate.com/v1/badges/b9ffbac3a93d9252c7cc/maintainability)](https://codeclimate.com/github/klaxurit/klaxurit/JuncaHugo_7_16122022/maintainability)
<h1 align="center">Welcome to BileMo API 👋</h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-Symfony 5.4-blue.svg?cacheSeconds=2592000" />
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
  <a href="https://twitter.com/axurit19" target="_blank">
    <img alt="Twitter: axurit19" src="https://img.shields.io/twitter/follow/axurit19.svg?style=social" />
  </a>
</p>

> An API for the BileMo Society.

## Prérequis

- PHP >= 8.1
- Composer

## Paquet installé via composer

- symfony/maker-bundle
- orm
- orm-fixtures
- security
- wildurand/hateoas-bundle
- sension/framework-extra-bundle
- symfony/validator
- doctrinne/annotations
- lexik/jwt-authentication-bundle
- nelmio/api-doc-bundle
- twig asset

## Installation

1. Clonez le dépôt git :
git clone https://github.com/klaxurit/klaxurit-JuncaHugo_6_16092022.git

2. Installez les dépendances en utilisant Composer :
composer install

3. Copiez le .env en .env.local et modifier les paramètres sql.

5. Créer un dossier jwt dans le dossier config et générez vos clefs grâce aux commandes suivantes:
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
Puis ajoutez la passphrase utilisez dans la variable JWT_PASSPHRASE de votre .env.local

6. Créez la base de données et effectuez les migrations en utilisant les commandes Doctrine :
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Exécution

Exécutez le serveur local Symfony pour lancer l'application :

php bin/console server:run

Vous devriez maintenant pouvoir accéder à la documentation de l'API en accédant à l'adresse `http://127.0.0.1:8000/api/doc` dans votre navigateur.

Pour chargez un jeux de donnée veuillez saisir cette commande dans votre terminal:

php bin/console doctrine:fixtures:load

Un compte test sera créer en plus des autres comptes.

### Compte test

- Identifiant: Companytest
- Mot de passe: password



## Autheur

👤 **JUNCA Hugo**

* Website: JUNCA Hugo
* Twitter: [@axurit19](https://twitter.com/axurit19)
* Github: [@klaxurit](https://github.com/klaxurit)
* LinkedIn: [@juncahugo](https://linkedin.com/in/juncahugo)

## Contribuer

Si vous souhaitez contribuer à ce projet, veuillez suivre les étapes suivantes :

1. Forkez ce dépôt
2. Créez une nouvelle branche (`git checkout -b nom_de_la_nouvelle_branche`)
3. Faites vos modifications
4. Commit et push sur votre branche (`git push origin nom_de_la_nouvelle_branche`)
5. Créez une pull request

***

