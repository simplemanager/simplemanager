Création de modèles

Les modèles sont très pratiques pour automatiser vos courriers ou vos e-mails. 
Cependant, leur création **requiert quelques connaissances techniques**. Nous vous 
conseillons d'utiliser les modèles existants qui sont régulièrement mis à jour.

> Un _modèle_ est une lettre type, qu'on associe à un destinataire ou à un 
> document pour générer des lettres automatiquement. Par exemple on peut 
> associer le modèle "Lettre de relance" à autant de factures que l'on veut, 
> pour éviter d'avoir à créer manuellement ce courrier à chaque envoi. 

Vous pouvez créer vos propres modèles ou dupliquer les modèles existants pour 
les personnaliser. Vous disposez d'une syntaxe simplifiée basée sur : 

* [Twig](https://twig.symfony.com|blank) pour le contenu, l'objet et les libellés.
* [Markdown](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet|blank) pour le contenu uniquement.

Téléchargez le **[guide de création des modèles](/files/guide-modeles-simplemanager.pdf|blank)** pour découvrir la liste 
des données que vous pouvez insérer dans un modèle ainsi que les fonctionnalités
disponibles.

> Un petit exemple de code source pour "tout document" :
> 
> * Le montant de votre {{ doc.type }} {{ doc.code }} est \**{{ doc.total_ttc }}**.
> 
> Qui donne pour une facture : 
> 
> * Le montant de votre facture F0001 est **34 €**.
> 
> Et pour une commande : 
> 
> * Le montant de votre commande C0023 est **645,40 €**.
