<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 

$admin_label = 'Administrateur';
$import_label = 'Produits importation';
$export_label = 'Produits d\'exportation';
$page_title_html = 'Documentation pour Joomla Component VM Products CSV <span style="color:#204D02;">!ULTIMATE!</span> v3 par <a href="http://www.ekerner.com/" title="Developer website" onclick="window.open(this.href); return false;">ekerner.com</a>';
if ($is_joomla) {
	$admin_label = '<a title="Joomla '.$admin_label.'" href="' . $admin_url . '">' . $admin_label . '</a>';
	$import_label = '<a title="' . $import_label . '" href="' . $admin_url . 'index.php?option=com_vm_products_csv&amp;view=import_products' . '">' . $import_label . '</a>';
	$export_label = '<a title="' . $export_label . '" href="' . $admin_url . 'index.php?option=com_vm_products_csv&amp;view=export_products' . '">' . $export_label . '</a>';
}
echo('
	<div>
		<div style="margin:0 40px;"><a href="http://www.ekerner.com.au/" title="eKerner - Application Developer" onclick="window.open(this.href); return false;"><img src="http://www.ekerner.com.au/images/logos/ekerner_technical_development_logo_nobg.png" alt="eKerner Logo" title="eKerner - App Developer" /></a></div>
		<h2 style="margin-left:40px;">' . $page_title_html . '</h2>
		<div style="margin:0 40px;">CSV Produit en vrac import/export/mise à jour pour VirtueMart.</div>
		<div style="margin:0 40px;">
			<h3>Langue ...</h3>
			<form method="post" action="" style="margin:0 40px;"><div>
				<select name="manual_lang" onchange="this.form.submit();">
					'.$manual_lang_select_options .'
				</select>
			</div></form>
		</div>
		<div style="margin:0 40px;">
			<h3>Menu ...</h3>
			<ul>
				<li><a title="" href="#usage">Utilisation</a></li>
				<li><a title="" href="#supported_fields">Les champs pris en charge</a></li>
				<li><a title="" href="#features">Caractéristiques</a></li>
				<li><a title="" href="#languages">Langues</a></li>
				<li><a title="" href="#notes">Remarques</a></li>
				<li><a title="" href="#screenshots">Captures d\'écran</a></li>
			</ul>
		</div>
		<a name="usage"></a>
		<div style="margin:0 40px;">
			<h3>Utilisation ...</h3>
			<div style="margin:0 40px;">
				Voir: ' . $admin_label . ' -&gt; Menu principal -&gt; Composants -&gt; VM Products CSV -&gt; ' . $import_label . '<br />
				Et: ' . $admin_label . ' -&gt; Menu principal -&gt; Composants -&gt; VM Products CSV -&gt; ' . $export_label . '
			</div>
			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<a name="supported_fields"></a>
		<div style="margin:0 40px;">
			<h3>Les champs pris en charge ...</h3>
			<ul>
				<li><b>virtuemart_vendor_id</b> :
					l\'ID du fournisseur, un entier positif. Par défaut 1. Si vous avez un seul utilisateur &lt;-&gt; configuration de VM multi fournisseur, alors vous pouvez modifier tous les produits des fournisseurs, fabricants, et les catégories. Si vous avez un multi-utilisateurs &lt;-&gt; de configuration VM multi fournisseurs, puis un administrateur peut toujours modifier tous les produits mais je ne recommande pas le partage des composants VM Produits CSV avec vos utilisateurs où ils seront en mesure de modifier chaque produit. Pour un multi-utilisateurs &lt;-&gt; de configuration VM multi fournisseurs vous auriez besoin de faire un écran pour permettre aux utilisateurs de soumettre leurs changements csv à l\'administrateur ou à une application qui étend / emploie  les Produits CSV.
				</li>
				<li><b>product_sku</b> :
					Si vous souhaitez mettre à jour un enregistrement existant alors vous devez le référencer par SKU (référence) dans votre CSV, et également s\'assurer que le mode \'mise à jour\' ou en mode \'Syncronisation\' est sélectionné. Sinon SKU (référence) est facultative et ne sera générée si non prévue. Si vous fournissez un duplicata SKU (référence) et le mode «Ajouter» est sélectionné, le dossier sera ignoré.
				</li>
				<li><b>product_parent_sku</b> (référence) :
					Pour déclarer un dossier comme un produit de l\'enfant puis ajouter son parent SKU (référence) ici. Parents et enfants peuvent apparaître dans n\'importe quel ordre.
				</li>
				<li><b>product_name</b> :
					Le titre de votre produit. Est également utilisé pour les titres de page et les méta-données non fournies.
				</li>
				<li><b>product_alias</b> :
					également connu comme \'slug\' et utilisés pour SEO URL conviviales. Les espaces et les caractères d\'URL non. Exemple: \'mon-produit-nom\'. Si pas fourni alors un alias de produit (slug) sera faite à partir du nom du produit.
				</li>
				<li><b>product_short_desc</b> :
					Brève description de produit selon les paramètres VM sur le terrain.
				</li>
				<li><b>product_desc</b> :
					Désignation du produit par les paramètres VM sur le terrain.
				</li>
				<li><b>meta_description</b> :
					Si non fournie, meta description est faite à partir sitename et le nom du produit.
				</li>
				<li><b>meta_keywords</b> :
					Si pas fournie alors les meta keywords sont fabriquées à partir du nom du produit.
				</li>
				<li><b>product_price</b> :
					Le \'prix de base du produit\' que vous devez entrer dans VM. Donc, si vous convertissez normalement pour l\'inclusion de la taxe, comme prix_moins_10_pour_cent_tax = (prix / 11) * 10, puis c\'est le prix pour entrer, plus dans cet exemple, vous devez également définir product_tax_id_ou_nom.
				</li>
				<li><b>product_currency_code</b> :
					Par exemple: \'EUR\'. Devises saisies doivent être installées et activées donc permises dans votre Joomla / VM.
				</li>
				<li><b>manufacturer_name</b> :
					Le nom du fabricant. Si le fabricant nommé n\'existe pas, il faut  \'permettre la création de fabricant\' donc la case doit être cochée, le fabricant sera créé avec fabricant_description et fabricant_image.
				</li>
				<li><b>manufacturer_desc</b> :
					La description pour le fabricant. Si le nom du frabricant existe et que la desription du fabricant est différente de la description du fabricant actuel alors il sera mis à jour.
				</li>
				<li><b>manufacturer_image</b> :
					L\'URL du fichier média d\'images pour le fabricant. Si le nom du fabricant existe et que les images du fabricant sont différentes du fabricant des images actuelles alors il sera mis à jour.
				</li>
				<li><b>category_id</b> :
					Entier positif Catégorie ID. Si vous mettez à jour une catégorie existante, alors il est nécessaire que vous  assigniez une catégorie à un produit intermédiare via categorie_id ou nom de la catégorie.
				</li>
				<li><b>category_name</b> :
					Le nom de la catégorie. Si catégorie nommée n\'existe pas, alors \'permettre la création de la catégorie \'et cocher la case, alors la catégorie sera créée avec categorie_description et categorie_image. Si categorie_id est fournie et categorie_nom est différent des formes à la categorie_nom actuel alors il sera mis à jour.
				</li>
				<li><b>category_desc</b> :
					Description de la catégorie. Si la catégorie existe et que la categorie_description est fournie et que la categorie_description diffère de la categorie_description actuelle alors il sera mis à jour.
				</li>
				<li><b>category_image</b> :
					Catégorie fichier multimédia URL de l\'image. Si la catégorie existe et que la categorie_image est fournie et que la categorie_image diffère de la categorie_image actuelle alors il sera mis à jour.
				</li>
				<li><b>category_parent_id_or_name</b> :
					La Catégorie ID ou Nom de la catégorie de la catégorie parente. Si fourni, alors il sera utilisé. Une valeur de \'Top catégorie de niveau\' \'0\' moyens. Si vous êtes à la recherche pour une catégorie de nom et vous ne savez pas l\'ID parent, ou si la catégorie parent nommé a été créé par un précédent record dans le même fichier CSV (et donc vous ne pouvez pas savoir encore l\'id), puis laisser ce champ vide / vide.
				</li>
				<li><b>product_tax_id_or_name</b> :
					Si vous souhaitez appliquer une taxe à ce produit alors passer son ID ou le nom ici. Par exemple: \'1\' ou \'GST Australien. Vous devez d\'abord vous connecter à votre administrateur Virtuemart, créer la taxe, et enregistrer / noter son ID ou Nom.
				</li>
				<li><b>product_discount_id_or_name</b> :
					Si vous souhaitez appliquer un rabais / offre de ce produit alors passer son ID ou le nom ici. Par exemple: \'2\' ou \'10 pour cent de réduction\'. Vous devez d\'abord vous connecter à votre administrateur Virtuemart, créer le rabais / offre, et enregistrer / noter son ID ou Nom.
				</li>
				<li><b>product_weight</b> :
					Poids du produit en nombre à virgule flottante, et selon product_weight_unit.
				</li>
				<li><b>product_weight_unit</b> :
					Produit unité de poids de mesure. Par exemple: \'kg\', \'g\'.
				</li>
				<li><b>product_length</b> :
					Longueur du produit comme nombre à virgule flottante, et selon produit_lwh_unit.
				</li>
				<li><b>product_width</b> :
					Largeur de produit comme nombre à virgule flottante, et selon produit_lwh_unit.
				</li>
				<li><b>product_height</b> :
					Hauteur du produit comme nombre à virgule flottante, et selon product_lwh_unit.
				</li>
				<li><b>product_lwh_unit</b> :
					Longueur du produit / largeur / unité de hauteur de mesure. Par exemple: \'mm\', \'cm\'.
				</li>
				<li><b>product_stock_quantity</b> :
					Le nombre de pièces en stock. La valeur par défaut \'0\'.
				</li>
				<li><b>product_availability</b> :
					Comme par VM Disponibilité du produit. Par défaut, \'En stock\'.
				</li>
				<li><b>featured_product</b> :
					Réglez à \'1\', \'y\' ou \'oui\' pour le produit vedette.
				</li>
				<li><b>published</b> :
					Réglez à \'1\', \'y\' ou \'oui\' pour pulier produit / permis. Par défaut, \'1\' si pas réglé.
				</li>
				<li><b>downloadable_media_id</b> :
					S\'applique uniquement si vous avez installé plg_vmcustom_downloadable (Voir <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Produits téléchargeables</a>). L\'ID de médias d\'un \'Vente\' Fichier Virtuemart médias. Parrainez vos Joomla Admin -> Virtuemart -> boutique -> Médias. La valeur par défaut \'0\'.
				</li>
				<li><b>downloadable_order_states</b> :
					S\'applique uniquement si vous avez installé plg_vmcustom_downloadable (Voir <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Produits téléchargeables</a>). Une virgule liste de noms de l\'état des commandes valides Virtuemart séparé. Exemple: \'Confirmé, Expédié\'. Vous pouvez trouver vos noms d\'état de commande dans votre Joomla Admin -> Virtuemart -> Configration -> Commander statuts (Je ne recommande pas de les modifier comme il peut gâcher certains plugins). Par défaut \'\' qui se traduit par \'Confirmé, expédiées\' si downloadable_media_id est réglé.
				</li>
				<li><b>downloadable_expiry</b> :
					S\'applique uniquement si vous avez installé plg_vmcustom_downloadable (Voir <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Produits téléchargeables</a>). La période après laquelle l\'expiration de votre lien de téléchargement. Format: \'Période de la quantité\', où la quantité est un iteger 0-11, et la période est \'jour\', \'semaine\', \'mois\', ou \'année\'. Exemples: \'six semaines\', \'1 année\', ou \'un année\', \'deux jours\'. Par défaut \'\' qui se traduit par \'ne expire jamais\' si downloadable_media_id est réglé.
				</li>
				<li><b>custom_cart_variants</b> :
					Champs de personnalisation du panier. Par example si le produit associé propose des couleurs comme le rouge et le bleu, ainsi que des tailles en petites et grandes, avec un surplus d\'un Euro supplémentaire pour la grande taille, alors réglez ce champ pour \'couleur:Rouge:0,couleur:Bleu:0,Taille:Petit:0,Taille:Large:1\'. NOTES: Pour chaque type déclaré (ex: couleur, Taille) Si une variante du panier n\'existe pas, alors elle sera créée. Les virgules (,) et les 2 points (:) ne peuvent pas apparaître dans le titre, la valeur ou le prix du champ personnalisé . Toutes variantes du panier existantes seront supprimées et les produits seront laissés avec les variantes du panier défini dans le CSV. 
				</li>
				<li><b>[ product_image_1 . . .</b> :
					Fichier multimédia de l\'image du produit URL. En ajouter autant que vous le souhaitez. Le mieux est de télécharger des images en premier.
				</li>
				<li><b>&nbsp;&nbsp;[ product_thumbnail_1 . . . ] ] </b> :
					Produit des médias d\'image pouce URL. Mieux est de laisser ce champ vide et laissez-VM générer les images miniatures. Le mieux est de télécharger des images d\'abord si vous allez l\'utiliser.
				</li>
			</ul>
			<div style="margin:0 40px;">... Ce qui implique nombre illimité d\'images, juste augmenter le nombre, c\'est à dire: produit_image_ &lt;numéro&gt;</div>
			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<a name="features"></a>
		<div style="margin:0 40px;">
			<h3>Caractéristiques ...</h3>
			<ul>
                                <li>Prend en charge trois modes d\'importation: \'Ajouter\' (ajouter uniquement des produits s\'ils n\'existent par SKU référence), \'Mise à jour\' (\'Ajouter\' plus mise à jour des produits existants par SKU), et \'Synchroniser\' (\'Mise à jour \'plus supprimer des produits ne se trouvent pas dans le CSV).</li>
                                <li>Accepte nombre illimité d\'images de produits avec des vignettes en option. Gère également catégorie et fabricant images, voir ci-dessous.</li>
                                <li>Valide chaque ligne avant d\'effectuer les opérations de base de données.</li>
                                <li>Génère des produits et Catégorie titre de la page, meta description et meta keywords où non fournis en utilisant des noms et des descriptions.</li>
                                <li>Prend en charge les configurations Joomla / Virtuemart multilingues. Utilise VM langues réglages. NOTE: En mode \'Syncronisation\' la suppression des produits n\'est pas biaisée par langue.</li>
                                <li>Valide passé identifiants (par exemple: virtuemart_vendeur_id, fabricant_id, produit_monnaie_code, produit_tax_id_ou_nom, produit_discount_id_ou_nom, etc) et prend toutes les précautions pour éviter l\'insertion de données invalides dans la base de données.</li>
                                <li>Prend en charge la création de la catégorie si non existante. Accepte nom, description, emplacement de l\'image, et ID parent ou le nom. Permet également la mise à jour du nom de la catégorie, la description, l\'emplacement de l\'image, et la catégorie de parent via CSV.</li>
                                <li>Alternative to the previous point, you can select \'Multiple Category Mode\' and associate products with multiple existing categories. </li>
                                <li>Crée Fabricant si non existante. Accepte nom, la description et l\'emplacement de l\'image. Permet également la mise à jour de la description du fabricant et emplacement de l\'image via CSV.</li>
                                <li>Switch \'Produit Vedette \'.</li>
                                <li>Switch \'Publié\'.</li>
                                <li>Permet Parent &lt;-&gt; de l\'association Enfant de produit.</li>
                                <li>Prise en charge de l\'affectation de la taxe sur le produit par ID ou le nom</li>
                                <li>Prise en charge de l\'affectation d\'actualisation Produit par ID ou le nom</li>
                                <li>Records poids du produit, dimensions, quantité de stock, et la disponibilité.</li>
                                <li>Contrôles pour les lignes existantes et effectue des opérations de mise à jour ou insertion.</li>
                                <li>Avertit si vous ajoutez une image qui n\'était pas encore ajoutée à la galerie des médias.</li>
                                <li>Supporte Virtuemart plugin personnalisé plg_vmcustom_téléchargeable (Voir <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Produits téléchargeables</a>).</li>
                                <li>Supporte champs variante de panier personnalisés comme  Taille and couleur.</li>
			</ul>
			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<a name="languages"></a>
		<div style="margin:0 40px;">
			<h3>Langues ...</h3>
                        <p>Toutes les langues sont traitées par l\'importateur et l\'exportateur. Tant que vous sélectionnez votre language et le caractère d\'encodage correct, faites ou exportez votre CSV dans l\'encodage de caractères correct, et vos paramètres régionaux du serveur sont corrects, vous pouvez importer / exporter dans toutes les langues.</p>

                        <p>Si votre langue n\'est pas dans la liste Jeu de caractères, il relève \'Default [CP1252]\'.</p>

                        <p>Votre codage de caractères de base de données est atteinte en interrogeant la base de données et si non déterminables par défaut à UTF8.</p>

                        <p>L\'interface supporte l\'anglais, italien, espagnol et français (auf ce document qui supporte l\'anglais et le français).<br/>
Cela signifie bien sûr que même si vous pouvez importer / exporter dans n\'importe quelle langue, vous aurez besoin de comprendre une langue d\'interface pris en charge pour utiliser l\'extension.<br/>
Si vous voulez une copie pour laquelle l\'interface prend en charge votre langue, puis travailler avec moi pour traduire les fichiers de langue et je vous donnerai une copie (réelle traduction).</p>

			<p>Traduction Française par <a href="http://www.lingerie-rebelle.com" title="lingerie-rebelle.com" onclick="window.open(this.href); return false;">Nathalie @ Lingerie Rebelle</a></p>

			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<a name="notes"></a>
		<div style="margin:0 40px;">
			<h3>Remarques ...</h3>
			<ul>
                                <li>Cette extension fonctionne sur votre base de données. Toujours faire des sauvegardes en premier. Si vous sélectionnez  un mauvais codage de caractères et que vous n\'avez pas fait une sauvegarde vous pouvez pleurer;)</li>
                                <li>Lors de l\'ajout d\'images: il est préférable si les images sont téléchargées en premier. Vous pouvez ajouter autant d\'images que vous le souhaitez à la fin des lignes CSV et les vignettes sont en option. Exemple: produit_image_1, produit_thumbnail_1, produit_image_2, produit_image_3, produit_thumbnail_3. Il est préférable de laisser les champs d\'image_thumbnail_N vierge et mettez VM pour générer automatiquement des vignettes.</li>
                                <li>Format CSV est standard: fiche delimiter = retour à la ligne, terrain delimiter = virgule, caractère d\'échappement de champ = guillemet.</li>
                                <li>L\'interface prend actuellement en anglais, italien, espagnol et français (Le manuel soutient anglais et en français). Si vous faites d\'autres fichiers de langue (traductions légitimes par une maîtrise humaine dans la langue, pas une app) s\'il vous plaît,  me le transmettre. Si j\'utilise votre fichier (s) langue je vous offre une extension libre avec plus de crédits  ainsi que  des liens pour vous des écrans d\'administration.</li>
                                <li>Certains champs sont nommés comme * _id_or_name. Pour ces domaines, vous ne pouvez pas passer un nom numérique ou il sera traité comme un identifiant. Ainsi, par exemple: si vous aviez une catégorie nommée \'1\' (pas très utile dans la recherche de documents comme les moteurs de recherche, mais vous pouvez le faire), avec l\'id de \'27\', et vous voulez faire référence à cette catégorie comme une catégorie de parent, puis dans le champ de category_parent_id_or_name vous devez entrer \'27\' pas \'1\'.</li>
                                <li>Conflit d\'enregistrement peut se produire si: Vous ajoutez produits, que vous avez fourni pour les dits références produits dans la CSV, vous avez déjà des produits dans votre boutique, et le mode d\'importation est \'mise à jour\' ou \'Synchronisée\'. Pour éviter les conflits d\'enregistrement / produits sélectionnez le mode d\'importation "Ajouter" lors de l\'ajout d\'abord produits dans ces conditions.</li>
                                <li>Vous pouvez supprimer tous les produits en sélectionnant le mode \'Synchroniser\' et l\'importation d\'un fichier CSV vierge / template (contenant uniquement la ligne de titre).</li>
                                <li>Produits / dossiers sans références seront assignés un unique faite d\'une combinaison du nom de produit abréviation et numéros inutilisés.</li>
                                <li>CSV codage de caractères par défaut CP1252 (Windows-1252 et Latin-1 compatible) qui couvre de nombreuses langues.</li>
                                <li>Le codage de caractères de vos fichiers CSV d\'importation doit correspondre à l\'encodage, vous sélectionnez. Si votre langue n\'est pas répertoriée dans le \'Jeu de caractères \'sélecteur, puis choisissez\' Par défaut [CP1252]\'.</li>
                                <li>Si votre avoir des problèmes avec des personnages rendant étrangement - soit dans votre feuille de calcul spectateur ou votre site Web - puis vérifiez votre jeu de caractères / encodage.</li>
                                <li>Votre codage de caractères de base de données est déterminé en interrogeant votre variable \'character_set_database\' de base de données.</li>
                                <li>Fonctionne en magasin multi-fournisseurs, mais si vous voulez permettre à vos fournisseurs d\'utiliser cela, alors vous devez ajouter quelques trucs. S\'il ne marche pas, vérifier que l\'utilisateur a un identifiant de fournisseur et qu\'il correspond à l\'ID de fournisseur fourni. En somme, si vos fournisseurs permettent de l\'utiliser comme un admin – cela pourrait modifier quelques produits, ce qui n\'est pas souhaité, je suppose. Donc, pour réitérer: Si vous avez la nécessité de laisser vos fournisseurs utiliser cette application alors nous avons besoin de faire quelques ajustements en premier.</li>
                                <li>J\'ai fait un bon plugin qui se plonge dans l\'admin produits VM Manager . Il semblait idéal à première vue toutefois plusieurs changemements, à cause de nombreux hacks sur divers produits admin vm CVM, ont été effectués. De plus,  étant donné le nombre (Id-dire plus) modèles de la boutique de vm et plugins ont la classe de VM et remplacements TMPL, il a été à l\'origine des conflits - comme de nombreux plugins vm déjà . Ce n\'est pas la faute de vm cependant, le problème est dans la conception du cadre Joomla et de ses techniques impérieuses. Quoi qu\'il en soit comme une composante, il est autonome.</li>
                                <li>Les champs commençant par \'downloadable_\' sont utilisés uniquement si plg_vmcustom_downloadable est installé (voir <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Produits téléchargeables</a>).</li>
			</ul>
			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<a name="screenshots"></a>
		<div style="margin:0 40px;">
			<h3>Captures d\'écran ...</h3>
			<div style="margin:0 40px;">
				<p><b>' . $import_label . '</b></p>
				<p><img style="width:80%; height:;" src="' . $admin_url . 'components/com_vm_products_csv/images/com_vm_products_csv_import_screen.png" alt="Import Screen" /></p>
			</div>
			<div style="margin:0 40px;">
				<p><b>' . $export_label . '</b></p>
				<p><img style="width:80%; height:;" src="' . $admin_url . 'components/com_vm_products_csv/images/com_vm_products_csv_export_screen.png" alt="Export Screen" /></p>
			</div>
			<p><a title="Top of page" href="#">[ Haut ]</a></p>
		</div>
		<div style="margin:40px;">Have fun,<br />Eugene Kerner.<br /><br /></div>
	</div>
');
?>
