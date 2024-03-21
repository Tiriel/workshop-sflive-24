# Symfony Live 2024 - Workshop
## Composants Messenger et Workflow

Ce projet servira de base pour les activités pratiques du Workshop.

### Configuration requise
Ce projet utilise Symfony 7 et tous ses prérequis, plus quelques ajouts:
- PHP 8.2
- Extensions CType, iconv, PCRE, Session, SimpleXML, Tokenizer
- Extension SQLite3 (ou autre BDD relationnelle en état de fonctionnement)
- Composer
- De préférence avoir le Symfony-CLI ([téléchargement](https://symfony.com/download))
### Installation

Procédure d'installation du projet (en commentaire, les commandes si vous n'avez pas le Symfony-CLI)

```shell
git clone https://github.com/Tiriel/workshop-sflive-24.git
cd workshop-sflive-24
symfony composer install
# composer install
symfony console doctrine:database:create
# php bin/console doctrine:database:create
symfony console doctrine:migrations:migrate
# php bin/console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
# php bin/console doctrine:fixtures:load
symfony serve -d
# php bin/console asset-map:compile
# php -S 0.0.0.0:8000 -t public/ &> /dev/null &
```
