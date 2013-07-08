<?php
/**
 * My Skin skin
 *
 * @file
 * @ingroup Skins
 * @author Garrett LeSage
 */

if( !defined( 'MEDIAWIKI' ) ) die( "This is an extension to the MediaWiki package and cannot be run standalone." );
 
$wgExtensionCredits['skin'][] = array(
        'path' => __FILE__,
        'name' => 'Labstrap',
        'url' => "https://github.com/OSAS/strapping-mediawiki",
        'author' => 'Garrett LeSage',
        'descriptionmsg' => 'labstrap-desc',
);

$wgValidSkinNames['labstrap'] = 'Labstrap';
$wgAutoloadClasses['SkinLabstrap'] = dirname(__FILE__).'/Labstrap.skin.php';
$wgExtensionMessagesFiles['SkinLabstrap'] = dirname(__FILE__).'/Labstrap.i18n.php';
 
$wgResourceModules['skins.labstrap'] = array(
        'styles' => array(
                'labstrap/bootstrap/css/bootstrap.min.css' => array( 'media' => 'screen' ),
                'labstrap/bootstrap/css/bootstrap-responsive.min.css' => array( 'media' => 'screen' ),
                'labstrap/css/labstrap.min.css' => array( 'media' => 'screen' )
	),
	'scripts' => array(
		'labstrap/bootstrap/js/bootstrap.js',
		'labstrap/js/labstrap.js',
	),
        'remoteBasePath' => &$GLOBALS['wgStylePath'],
        'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);

# Default options to customize skin
// FIXME: Check if these vars are consistent with the modifications
$wgLabstrapSkinLogoLocation = 'bodycontent';
$wgLabstrapSkinLoginLocation = 'footer';
$wgLabstrapSkinAnonNavbar = false;
$wgLabstrapSkinUseStandardLayout = false;
$wgLabstrapSkinDisplaySidebarNavigation = false;
# Show print/export in navbar by default
$wgLabstrapSkinSidebarItemsInNavbar = array( 'coll-print_export' );
# Globals to enable search autocompletion in mediawiki 1.19
# Doesn't act like the vector extension simplesearch improvements
# (missing 'containing...' in the results ).
# This feature has since been implemented in mediawiki
# https://git.wikimedia.org/commitdiff/mediawiki%2Fextensions%2FVector/55a6f5a2534162494d7cb624ca37258fa81db584
$wgUseAjax = true;
$wgEnableMWSuggest = true;