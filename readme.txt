=== YD Media-Pay WP Plugin ===
Contributors: ydubois
Donate link: http://www.yann.com/
Tags: paywall, micropayment, micropaiement, pay, paiement, monétisation, monetize, monetization, member, members, registration, mediapay, telephone, pass, code
Requires at least: 2.9
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Permet de monétiser les contenus ou l'inscription à un site WordPress via la plateforme de micro-paiement par téléphone Media Pay.

== Description ==

= Comment rendre vos contenus payants ? =

Sans abonnement et simplement en ouvrant un compte en ligne sur la plateforme [Media-Pay](http://www.media-pay.fr/ "Plate-forme de micro-paiement téléphonique"), et en activant ce plugin, vous pouvez rendre l'accès à tous vos contenus payants :

* Possibilité de rendre tout le site payant, ou seulement certaines pages.

* Fonctionne pour les articles (posts) ou les pages, ou les deux.

* Possibilité de conserver certains articles gratuits.

* Possibilité de ne rendre que quelques articles payants.

* Possibilité de proposer différents tarifs.

* Gestion de différents pays ou langues, pour l'ensemble du site ou page par page.

* Possibilité d'avoir des codes d'accès différents pour certaines pages.

* Possibilité de rendre l'inscription au site en tant que membre payante.

* Compatible avec PHP5 et supérieur uniquement.

* Compatible avec WordPress 3.x, les thèmes livrés avec WordPress par défaut, et tous les thèmes les plus courants.

* Possibilité de désactiver la feuille de style incluse pour personnaliser entièrement votre interface de paiement.

* Compatible avec WordPress mono ou multi-sites.

* N'utilise que la structure de donnée interne à WordPress, pas de table ajoutée.

* L'installation, la configuration et les interfaces du plugin sont entièrement en français.

* Le système de paiement fonctionne notamment pour la France et la Belgique.


= Support actif en français =

Via le [forum des utilisateurs et la FAQ de la solution Media-Pay](http://www.media-pay.fr/faq-media-pay.php),
ou via le site du développeur [Support plugin YD Media-Pay pour WP](http://www.yann.com/en/wp-plugins/mediapay-micropaiement "Yann Dubois' Media-Pay plugin").


= Financement =

Le développement original de ce plugin a été financé par [Media Technologies](http://www.media-technologies.eu/ "Media Technologies"),
éditeur de la solution Media-Pay. Allez visiter leur site !


== Installation ==

L'installation automatique depuis l'interface d'administration de WordPress est recommandée.

Une fois le plugin installé et activé, rendez-vous sur la page Réglages > MediaPay dans l'interface d'administration de votre site WordPress.

Ouvrez votre compte gratuitement sur [la page d'inscription du service Media-Pay](http://www.media-pay.fr/inscription-media-pay.php). Déclarez votre site avec son nom de domaine exact, et mettez une étoile (*) dans les champs "Url de la page d'accès", "Url de la zone protégée" et "Url de retour sur erreur" de la page de configuration du site, de façon à pouvoir appliquer les micropaiements à n'importe quelle page de votre site WordPress.

Recopiez votre numéro d'identification dans la page de réglages de MediaPay dans votre interface d'administration WordPress, cliquez les cases correspondant aux options que vous souhaitez mettre en oeuvre, puis cliquez sur le bouton "MAJ des réglages" : c'est fait !

Si vous souhaitez monétiser uniquement certains articles de votre site, ne cochez pas les cases "Monétiser tous les articles" et "Monétiser toutes les pages", vous pouvez activer la monétisation sur un article spécifique en ajoutant un champ personnalisé "mediapay" avec une valeur de "payant" (ou "pay" ou "charged") à cet article.

Autres options avancées :

* Pour qu'un article soit toujours gratuit, même si l'option "Monétiser tous les articles" est activée, il suffit de lui ajouter un champ personnalisé "mediapay" avec comme valeur "gratuit" ou "free" ou "gratis".

* Pour utiliser un identifiant spécifique (et un code d'accès ou un tarif différent) sur un article spécifique, préciser l'identifiant mediapay de la page correspondante dans un champ personnalisé "mediapay".

* Pour spécifier un code tarif par pays pour certains articles (par exemple si votre site dispose de pages dans différentes langues ou d'offres s'adressant à différents pays), vous pouvez préciser le code de tarif à utiliser sur l'article avec un champ personnalisé "mediapay_tarif" dont la valeur est le code MediaPay (voir sur le site MediaPay la liste des codes). Par exemple, pour la Belgique : "TARIF_AUDIO_BE", ou pour la France : "TARIF_AUDIO_FR".


== Questions fréquentes ==

= Où poser des questions ? =

http://www.media-pay.fr/faq-media-pay.php


= Puis-je poser des questions et avoir des docs en français ? =

Oui, l'auteur est français.
("but alors... you are French?")


== Screenshots ==

1. Paiement de contenu configuré pour un pays
2. Paiement de contenu configuré pour plusieurs pays
3. Inscription payante
4. Réglages des options du plugin


== Revisions ==

* 0.1.2. Mise à jour de la documentation et petit correctif 30/04/2013
* 0.1.1. Mise à jour de la documentation 29/04/2013
* 0.1.0. Première version du 26/04/2013


== Changelog ==

= 0.1.2 =
* Documentation fix
* Small bugfix
= 0.1.1 =
* Documentation upgrade
= 0.1.0 =
* Initial beta release


== Upgrade Notice ==

= 0.1.2 =
* No specifics. Automatic upgrade works fine.
= 0.1.1 =
* No specifics. Automatic upgrade works fine.
= 0.1.0 =
* No specifics. Automatic upgrade works fine.


== Vous aimez ? ==

Laissez-moi un commentaire sur http://www.yann.com/en/wp-plugins/mediapay-micropaiement

Et... *SVP* évaluez cette extension --&gt;