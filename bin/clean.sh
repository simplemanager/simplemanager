#!/bin/bash

ISROOT=`expr $USER = 'root'`

echo

# ==============================================================================
# Variables
# ==============================================================================

HOSTNAME=`hostname`
THIS_FILE=$(readlink -f $0)
WORK_DIR=$(dirname $(dirname $THIS_FILE))
# LIB_DIR=$WORK_DIR/vendor/osflab

WRN='\033[1;31m'
INF='\033[1;32m'
EXT='\033[0;37m'
GR='\033[1;37m'
NC='\033[0m'

if [ $ISROOT -eq 0 ] ;then
  HTTP_USER='www-data'
  HTTP_GROUP='www-data'
fi
# WORK_USER=`stat -c %U $0`
# WORK_GROUP=`stat -c %G $0`

# ==============================================================================
# Détection de l'environnement et des utilisateurs locaux
# ==============================================================================

if [ $HOSTNAME == 'darkone' -o $HOSTNAME == 'redone' -o $HOSTNAME == 'simplemanager' ] ;then
  WORK_USER=`stat -c %U $0`
  ENV_TYPE='dev'
  echo -e "-> Environnement détecté : [${GR}développement${NC}] (${INF}"$HOSTNAME"${NC})"
elif [ $HOSTNAME == 'oleo' ] ;then
  WORK_USER=`stat -c %U $0`
  ENV_TYPE='test'
  echo -e "-> Environnement détecté : [${GR}test${NC}] (${INF}"$HOSTNAME"${NC})"
elif [ $HOSTNAME == 'od-71f13c' ] ;then
  WORK_USER=`stat -c %U $0`
  ENV_TYPE='prod'
  echo -e "-> Environnement détecté : [${GR}production${NC}] (${INF}"$HOSTNAME"${NC})"
else
  WORK_USER=$HTTP_USER
  ENV_TYPE='prod'
  echo -e "-> Environnement détecté : [${GR}production${NC}] (hôte non répertorié ${WRN}"$HOSTNAME"${NC})"
fi

if [ $ISROOT -eq 0 ] ;then
  echo -e "-> Niveau d'exécution du script de nettoyage : [${WRN}non root${NC}]"
else 
  echo -e "-> Niveau d'exécution du script de nettoyage : [${INF}root${NC}]"
fi

# ==============================================================================
# Update SVN
# ==============================================================================

if [ 'xupdate' == "x$@" ] ;then
  echo -e "-> Début de mise à jour depuis SVN...${EXT}"
  cd $WORK_DIR
  sudo -u $WORK_USER svn update
  if [ $? != 0 ] ;then
    echo -e "${NC}-> ${WRN}Erreur de mise à jour SVN${NC}";
    exit 1
  fi
  echo -e "${NC}-> Fin de mise à jour SVN"
fi

# ==============================================================================
# Nettoyages
# ==============================================================================

if [ ! -d $WORK_DIR'/htdocs/www/img' ] ;then
  echo '-> Création du répertoire htdocs/www/img'
  mkdir $WORK_DIR'/htdocs/www/img'
fi

echo '-> Nettoyage des fichiers temporaires'
rm -f $WORK_DIR'/htdocs/www/img/'*

echo '-> Check des fichiers de configuration locaux'
CONF_DATA="<?php\n\n// "$HOSTNAME" ("$ENV_TYPE") specific configuration\n\nreturn [];\n\n// vim: encoding=utf-8\n"
CONF_FILE=$WORK_DIR'/etc/application.php'
if [ ! -f $CONF_FILE ] ;then
  echo -e $CONF_DATA > $CONF_FILE
  echo "-> Création de "$CONF_FILE
  echo -e "-> ${WRN}Ce fichier de configuration doit être rempli.${NC}"
fi

# ==============================================================================
# Fix des droits
# ==============================================================================

if [ $ISROOT -eq 0 ] ;then
  echo -e "-> Mise à jour des utilisateurs/groupes : ${WRN}impossible${NC}"
else 
  echo -e "-> Tous les fichiers appartiennent à l'utilisateur [${GR}"$WORK_USER"${NC}]"
  echo -e "-> Tous les fichiers appartiennent au groupe [${GR}"$HTTP_GROUP"${NC}]"
  chown -R $WORK_USER:$HTTP_GROUP $WORK_DIR
#  chown -RL $WORK_USER:$HTTP_GROUP $LIB_DIR
fi

echo "-> Droits par défaut sur les fichiers : 644"
find $WORK_DIR -type f -print0 | xargs -0 chmod 644

echo "-> Droits par défaut sur les exécutables (/bin) : 750"
find $WORK_DIR'/bin' -type f -print0 | xargs -0 chmod 750

echo "-> Droits par défaut sur les dossiers : 755"
find $WORK_DIR -type d -print0 | xargs -0 chmod 755

echo "-> Droits d'écriture spécifiques à apache"
find $WORK_DIR'/var'            -type f -print0 | xargs -0 chmod 666 2> /dev/null
find $WORK_DIR'/var'            -type d -print0 | xargs -0 chmod 777
find $WORK_DIR'/htdocs/www/img' -type f -print0 | xargs -0 chmod 666 2> /dev/null
find $WORK_DIR'/htdocs/www/img' -type d -print0 | xargs -0 chmod 777

if [ -d $WORK_DIR'/frontend' ] ;then
  echo "-> Droits spécifiques aux sources VueJS/Webpack"
  find $WORK_DIR'/frontend' -type f -print0 | xargs -0 chmod 660 2> /dev/null
  find $WORK_DIR'/frontend' -type d -print0 | xargs -0 chmod 770
fi

# echo "-> Droits sur les fichiers et dossiers des librairies"
# find $LIB_DIR'/' -type f -print0 | xargs -0 chmod 644
# find $LIB_DIR'/' -type d -print0 | xargs -0 chmod 755

# ==============================================================================
# Définition des ACLs afin d'éviter les problèmes d'accès après déploiements
# ==============================================================================

#if [ $ENV_TYPE != 'dev' ] ;then
#  echo "-> Remise en place des ACLs sur les libs et l'espace de travail"
#  setfacl -Rb $WORK_DIR
#  setfacl -Rm u:www-data:rwx $WORK_DIR
#  setfacl -Rdm u:www-data:rwx $WORK_DIR
#  setfacl -Rb $LIB_DIR
#  setfacl -Rm u:www-data:rwx $LIB_DIR
#  setfacl -Rdm u:www-data:rwx $LIB_DIR
#fi

# ==============================================================================
# Fin de process
# ==============================================================================

if [ $ISROOT = 1 ] ;then
  if [ 'xupdate' != "x$@" ] ;then
    echo -e "-> Pour mettre à jour depuis SVN, executer ${INF}./"$(basename $THIS_FILE)" update${NC}"
  fi
fi

echo 
