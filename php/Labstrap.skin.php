<?php
/**
 * Vector - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
  die( -1 );
}

/**
 * SkinTemplate class for Vector skin
 * @ingroup Skins
 */
class SkinLabstrap extends SkinTemplate {

  var $skinname = 'labstrap', $stylename = 'labstrap',
    $template = 'LabstrapTemplate', $useHeadElement = true;

  /**
   * Initializes output page and sets up skin-specific parameters
   * @param $out OutputPage object to initialize
   */
  public function initPage( OutputPage $out ) {
    global $wgLocalStylePath;

    parent::initPage( $out );

    // Append CSS which includes IE only behavior fixes for hover support -
    // this is better than including this in a CSS fille since it doesn't
    // wait for the CSS file to load before fetching the HTC file.
    $min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
    $out->addHeadItem( 'csshover',
      '<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
        htmlspecialchars( $wgLocalStylePath ) .
        "/{$this->stylename}/js/csshover{$min}.htc\")}</style><![endif]-->"
    );

    $out->addHeadItem('responsive', '<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    $out->addModuleScripts( 'skins.labstrap' );
  }

  /**
   * Load skin and user CSS files in the correct order
   * fixes bug 22916
   * @param $out OutputPage object
   */
  function setupSkinUserCss( OutputPage $out ) {
        global $wgResourceModules;

    parent::setupSkinUserCss( $out );

    // FIXME: This is the "proper" way to include CSS
    // however, MediaWiki's ResourceLoader messes up media queries
    // See: https://bugzilla.wikimedia.org/show_bug.cgi?id=38586
    // &: http://stackoverflow.com/questions/11593312/do-media-queries-work-in-mediawiki
    //
    //$out->addModuleStyles( 'skins.labstrap' );

    // Instead, we're going to manually add each, 
    // so we can use media queries
    foreach ( $wgResourceModules['skins.labstrap']['styles'] as $cssfile => $cssvals ) {
      if (isset($cssvals)) {
        $out->addStyle( $cssfile, $cssvals['media'] );
      } else {
        $out->addStyle( $cssfile );
      }
    }
  
  } 
}

/**
 * QuickTemplate class for Vector skin
 * @ingroup Skins
 */
class LabstrapTemplate extends BaseTemplate {

  /* Functions */

  /**
   * Outputs the entire contents of the (X)HTML page
   */
  public function execute() {
    global $wgGroupPermissions;
    global $wgVectorUseIconWatch;
    global $wgSearchPlacement;
    global $wgLabstrapSkinLogoLocation;
    global $wgLabstrapSkinLoginLocation;
    global $wgLabstrapSkinAnonNavbar;
    global $wgLabstrapSkinUseStandardLayout;

    if (!$wgSearchPlacement) {
      $wgSearchPlacement['header'] = true;
      $wgSearchPlacement['nav'] = false;
      $wgSearchPlacement['footer'] = false;
    }

    // Build additional attributes for navigation urls
    $nav = $this->data['content_navigation'];

    if ( $wgVectorUseIconWatch ) {
      $mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
      if ( isset( $nav['actions'][$mode] ) ) {
        $nav['views'][$mode] = $nav['actions'][$mode];
        $nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
        $nav['views'][$mode]['primary'] = true;
        unset( $nav['actions'][$mode] );
      }
    }

    $xmlID = '';
    foreach ( $nav as $section => $links ) {
      foreach ( $links as $key => $link ) {
        if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
          $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
        }

        $xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
        $nav[$section][$key]['attributes'] =
          ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
        if ( $link['class'] ) {
          $nav[$section][$key]['attributes'] .=
            ' class="' . htmlspecialchars( $link['class'] ) . '"';
          unset( $nav[$section][$key]['class'] );
        }
        if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
          $nav[$section][$key]['key'] =
            Linker::tooltip( $xmlID );
        } else {
          $nav[$section][$key]['key'] =
            Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
        }
      }
    }
    $this->data['namespace_urls'] = $nav['namespaces'];
    $this->data['view_urls'] = $nav['views'];
    $this->data['action_urls'] = $nav['actions'];
    $this->data['variant_urls'] = $nav['variants'];

    // Output HTML Page
    $this->html( 'headelement' );
?>

<div id="mw-page-base" class="noprint"></div>
<div id="mw-head-base" class="noprint"></div>

<!-- Header -->
<header id="page-header" class="header container noprint">

    <div class="row labstrap-card labstrap-card-light">
      <!-- logo -->
      <div class="logo col-md-6 col-sm-6">
        <button class="btn btn-default btn-lg labstrap-header-menu-button"
                href="#"
                data-toggle="slide"
                data-target="#card-slide-nav">
          <span class="sr-only">Toggle header navigation menu</span>
          <i class="fa fa-lg fa-reorder"></i>
        </button>
        <?php
        if ( $wgLabstrapSkinLogoLocation == 'bodycontent' ) {
          $this->renderLogo();
        } ?>
      </div>
      <!-- Search and personal menu -->
      <div class="col-md-6 col-sm-6 navbar navbar-transparent">
        <div class="navbar-right">
          <?php
            if ($wgSearchPlacement['header']) {
              $this->renderNavigation( array( 'SEARCH' ) ); 
            }
          ?>
          <ul class="nav navbar-nav navbar-right hidden-xs" role="navigation">
            <?php $this->renderNavigation( array( 'PERSONAL_LISTITEMS' ) ); ?>
          </ul>
        </div>
      </div>
    </div>

    <nav class="row labstrap-card labstrap-card-dark labstrap-card-slide" id="card-slide-nav">
      <button class="close close-inverse"
              href="#"
              data-toggle="slide"
              data-target="#card-slide-nav">
        &times;
      </button>
      <ul class="nav navbar-nav nav-pills nav-pills-inverse searchform-disabled">
        <?php $this->renderNavigation( array( 'PERSONAL_LISTITEMS_VISIBLE_XS' ) ); ?>
        <?php
        $this->renderNavigation( array( 'SIDEBAR' ) );
        // Horizontal accordion search fo navbar
        // if ($wgSearchPlacement['nav']) {
        //   $this->renderNavigation( array( 'SEARCHNAV' ) );
        // }
        ?>
      </ul>
    </nav>
</header>

<?php
if ($this->data['loggedin']) {
  $userStateClass = "user-loggedin";
} else {
  $userStateClass = "user-loggedout";
}
?>

<?php if ($wgGroupPermissions['*']['edit'] || $this->data['loggedin']) {
  $userStateClass += " editable";
} else {
  $userStateClass += " not-editable";
}
?>

<!-- content -->
<?php /* FIXME: echo $useuserStateClass  returns 0? */ ?>
<section id="content" class="mw-body container <?php echo $userStateClass; ?>">
  <div id="top"></div>
  <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
  <?php if ( $this->data['sitenotice'] ): ?>
  <!-- sitenotice -->
  <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
  <!-- /sitenotice -->
<?php endif; ?>
<!-- bodyContent -->
<div id="bodyContent" class="row labstrap-card labstrap-card-light">
  <?php if( $this->data['newtalk'] ): ?>
  <!-- newtalk -->
  <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
  <!-- /newtalk -->
<?php endif; ?>
<?php if ( $this->data['showjumplinks'] ): ?>
  <!-- jumpto -->
  <div id="jump-to-nav" class="mw-jump">
    <?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
    <a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
  </div>
  <!-- /jumpto -->
<?php endif; ?>

<!-- innerbodycontent -->
        <?php # Peek into the body content, to see if a custom layout is used
        if ($wgLabstrapSkinUseStandardLayout || preg_match('/<div.*class="[^"]*labstrap_custom_layout[^"]*"/', $this->data['bodycontent'])) { 
          # If there's a custom layout, the H1 and layout is up to the page ?>
          <div id="innerbodycontent" class="layout">
            <!-- subtitle -->
            <div id="contentSub" <?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
            <!-- /subtitle -->
            <?php if ( $this->data['undelete'] ): ?>
            <!-- undelete -->
            <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
            <!-- /undelete -->
            <?php endif; ?>
          <?php $this->html( 'bodycontent' ); ?>
        </div>
        <?php } else {
          # If there's no custom layout, then we automagically add one ?>
          <div id="innerbodycontent" class="nolayout">
            
            <div class="content-heading-container">

              <?php /* Page headers are inserted here so that the navbar will appear under them in smaller displays */ ?>

              <h1 id="firstHeading" class="visible-xs firstHeading page-header">
                <span dir="auto"><?php $this->html( 'title' ) ?></span>
              </h1>
              <!-- subtitle -->
              <?php if ($this->data['subtitle']): ?>
              <div id="contentSub" class="visible-xs well well-small" <?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
              <?php endif; ?>
              <!-- /subtitle -->
              <!-- undelete -->
              <?php if ( $this->data['undelete'] ): ?>
              <div id="contentSub2" class="visible-xs well well-small"><?php $this->html( 'undelete' ) ?></div>
              <?php endif; ?>
              <!-- /undelete -->

              <!-- page actions -->
              <div class="navbar navbar-md-transparent navbar-default navbar-right noprint">

                <div class="navbar-header">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#page-actions-navbar-collapse">
                    <span class="sr-only">Toggle page actions</span>
                    <i class="fa fa-wrench"></i>
                  </button>
                  <div class="navbar-brand visible-xs">Page Actions</div>
                </div>

                <div class="collapse navbar-collapse" id="page-actions-navbar-collapse">
                  <ul class="nav navbar-nav navbar-right" role="navigation">
                    <?php
                      // if ( $wgLabstrapSkinLogoLocation == 'navbar' ) {
                      //   $this->renderLogo();
                      // }

                      # Page header & menu
                      $this->renderNavigation( array( 'PAGE' ) );

                      # This content in other languages
                      if ( $this->data['language_urls'] ) {
                        $this->renderNavigation( array( 'LANGUAGES' ) );
                      }
                      # Actions menu
                      $this->renderNavigation( array( 'ACTIONS' ) ); 

                      if ( !isset( $portals['TOOLBOX'] ) ) {
                        $this->renderNavigation( array( 'TOOLBOX' ) ); 
                      }
                      # Sidebar items to display in navbar
                      // $this->renderNavigation( array( 'SIDEBARNAV' ) );
                    ?>
                    
                    <li>
                      <?php
                        # Edit button
                        $this->renderNavigation( array( 'EDIT' ) ); 
                      ?>
                    </li>
                  </ul>
                </div>
              </div>
              <!-- page actions -->

              <?php /* Page headers are re-inserted here so that the navbar will float correctly to the right on bigger displays */ ?>

              <h1 id="firstHeading" class="hidden-xs firstHeading page-header">
                <span dir="auto"><?php $this->html( 'title' ) ?></span>
              </h1>
              <!-- subtitle -->
              <?php if ($this->data['subtitle']): ?>
              <div id="contentSub" class="hidden-xs well well-small" <?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
              <?php endif; ?>
              <!-- /subtitle -->
              <!-- undelete -->
              <?php if ( $this->data['undelete'] ): ?>
              <div id="contentSub2" class="hidden-xs well well-small"><?php $this->html( 'undelete' ) ?></div>
              <?php endif; ?>
              <!-- /undelete -->

            </div>
          <?php $this->html( 'bodycontent' ); ?>
        </div>
        <?php } ?>
        <!-- /innerbodycontent -->

        <?php if ( $this->data['printfooter'] ): ?>
        <!-- printfooter -->
        <div class="printfooter">
          <?php $this->html( 'printfooter' ); ?>
        </div>
        <!-- /printfooter -->
      <?php endif; ?>
      <?php if ( $this->data['catlinks'] ): ?>
      <!-- catlinks -->
      <?php $this->html( 'catlinks' ); ?>
      <!-- /catlinks -->
    <?php endif; ?>
    <?php if ( $this->data['dataAfterContent'] ): ?>
    <!-- dataAfterContent -->
    <?php $this->html( 'dataAfterContent' ); ?>
    <!-- /dataAfterContent -->
  <?php endif; ?>
  <div class="visualClear"></div>
  <!-- debughtml -->
  <?php $this->html( 'debughtml' ); ?>
  <!-- /debughtml -->
</div>
<!-- /bodyContent -->
</section>
<!-- /content -->

<!-- footer -->
<footer id="footer" class="footer container"<?php $this->html( 'userlangattributes' ) ?>>
      <div class="row labstrap-card labstrap-card-dark">
      <?php
      /* Make footer static for now, maybe add/remove links via mediawiki later? */
      /* http://www.mediawiki.org/wiki/Manual:Footer */
      $footerLinks = /*$this->getFooterLinks()*/NULL;
      ?>

      <ul id="footer-places">
        <?php
          if (!$this->data['loggedin']) {
            $personalTemp = $this->getPersonalTools();

          if (isset($personalTemp['login'])) {
            $loginType = 'login';
          } else {
            $loginType = 'anonlogin';
          }

          ?><li id="pt-login"><a href="<?php echo $personalTemp[$loginType]['links'][0]['href'] ?>"><?php echo $personalTemp[$loginType]['links'][0]['text']; ?></a></li><?php
        }?>
        <li><a href="/w/To_Labaki:About" title="To Labaki:About"><i class="fa fa-info-circle fa-lg"></i> Σχετικά</a></li>
        <li><a href="/w/Contact" title="Contact"><i class="fa fa-comments fa-lg"></i> Επικοινωνία</a></li>
        <li><a href="https://twitter.com/tolabaki"><i class="fa fa-twitter fa-lg"></i> Twitter</a></li>
        <li><a href="https://www.facebook.com/ToLABaki"><i class="fa fa-facebook fa-lg"></i> Facebook</a></li>
      </ul>

      <ul>
        <li>Content licenced under <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a></li>      
        <li>Powered by <a href="https://www.mediawiki.org/wiki/MediaWiki">Mediawiki</a></li>    
      </ul>

      <?php
      if (is_array($footerLinks)) {
        foreach($footerLinks as $category => $links ):
          if ($category === 'info') { continue; } ?>

        <ul id="footer-<?php echo $category ?>">
          <?php foreach( $links as $link ): ?>
          <li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
        <?php endforeach; ?>
        <?php
        if ($category === 'places') {

                  # Show sign in link, if not signed in
          if ($wgLabstrapSkinLoginLocation == 'footer' && !$this->data['loggedin']) {
            $personalTemp = $this->getPersonalTools();

            if (isset($personalTemp['login'])) {
              $loginType = 'login';
            } else {
              $loginType = 'anonlogin';
            }

            ?><li id="pt-login"><a href="<?php echo $personalTemp[$loginType]['links'][0]['href'] ?>"><?php echo $personalTemp[$loginType]['links'][0]['text']; ?></a></li><?php
          }

                  # Show the search in footer to all
          if ($wgSearchPlacement['footer']) {
            echo '<li>';
            $this->renderNavigation( array( 'SEARCHFOOTER' ) ); 
            echo '</li>';
          }
        }
        ?>
      </ul>
      <?php 
      endforeach; 
    }
    ?>

    <?php /*$footericons = $this->getFooterIcons("icononly");
    if ( count( $footericons ) > 0 ):*/ ?>
    <!-- <ul id="footer-icons" class="noprint"> -->
      <?php      /*foreach ( $footericons as $blockName => $footerIcons ):*/ ?>
      <!-- <li id="footer-<?php /*echo htmlspecialchars( $blockName );*/ ?>ico"> -->
        <?php        /*foreach ( $footerIcons as $icon ):*/ ?>
        <?php /*echo $this->getSkin()->makeFooterIcon( $icon );*/ ?>

      <?php      /*  endforeach;*/ ?>
    <!-- </li> -->
  <?php      /*endforeach;*/ ?>
<!-- </ul> -->
<?php /*endif;*/ ?>
</div>
</footer>
<!-- /footer -->

    <?php $this->printTrail(); ?>

  </body>
</html>

<?php
  }

  /**
   * Render logo
   */
  private function renderLogo() {
        $mainPageLink = $this->data['nav_urls']['mainpage']['href'];
        $toolTip = Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) );
?>
                  <!-- <ul class="nav" role="navigation"><li id="p-logo"><a href="<?php /*echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] )*/ ?>" <?php /*echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) )*/ ?>><img src="<?php /*$this->text( 'logopath' );*/ ?>" alt="<?php /*$this->html('sitename');*/ ?>"></a><li></ul> -->
  <a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>><img src="<?php $this->text( 'logopath' ); ?>" alt="<?php $this->html('sitename'); ?>"></a>
<?php
  }

  /**
   * Render one or more navigations elements by name, automatically reveresed
   * when UI is in RTL mode
   *
   * @param $elements array
   */
  private function renderNavigation( $elements ) {
    global $wgVectorUseSimpleSearch;
    global $wgLabstrapSkinLoginLocation;
    global $wgLabstrapSkinDisplaySidebarNavigation;
    global $wgLabstrapSkinSidebarItemsInNavbar;

    // If only one element was given, wrap it in an array, allowing more
    // flexible arguments
    if ( !is_array( $elements ) ) {
      $elements = array( $elements );
    // If there's a series of elements, reverse them when in RTL mode
    } elseif ( $this->data['rtl'] ) {
      $elements = array_reverse( $elements );
    }
    // Render elements
    foreach ( $elements as $name => $element ) {
      echo "\n<!-- {$name} -->\n";
      switch ( $element ) {

        case 'EDIT':
          if ( !array_key_exists('edit', $this->data['content_actions']) ) {
            break;
          }
          $navTemp = $this->data['content_actions']['edit'];

          if ($navTemp) { ?>
            <div id="b-edit" class="navbar-btn hidden-xs">
              <a href="<?php echo $navTemp['href']; ?>" class="btn btn-primary">
                <i class="fa fa-edit fa-lg"></i>
                <?php echo $navTemp['text']; ?>
              </a>
            </div>
            <a href="<?php echo $navTemp['href']; ?>" class="visible-xs">
              <i class="fa fa-edit fa-lg"></i>
              <?php echo $navTemp['text']; ?>
            </a>
          <?php } 
        break;


        case 'PAGE':
          $theMsg = 'namespaces';
          $theData = array_merge($this->data['namespace_urls'], $this->data['view_urls']);
          ?>
          <!-- <ul class="nav" role="navigation"> -->
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <?php
              foreach ( $theData as $link ) {
                  if ( array_key_exists( 'context', $link ) && $link['context'] == 'subject' ) {
              ?>
              <a data-toggle="dropdown" class="dropdown-toggle" role="menu" href="#"><?php echo htmlspecialchars( $link['text'] ); ?> <b class="caret"></b></a>
                  <?php } ?>
              <?php } ?>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

                <?php 
                foreach ( $theData as $link ) {
                  # Skip a few redundant links
                  if (preg_match('/^ca-(view|edit)$/', $link['id'])) { continue; }

                  ?><li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li><?php
                }

          ?></ul></li><!-- </ul> --><?php

        break;


        case 'TOOLBOX':

          $theMsg = 'toolbox';
          $theData = array_reverse($this->getToolbox());
          ?>

          <!-- <ul class="nav" role="navigation"> -->

            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">

              <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>

              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

                <?php
                  foreach( $theData as $key => $item ) {
                    if (preg_match('/specialpages|whatlinkshere/', $key)) {
                      echo '<li class="divider"></li>';
                    }

                    echo $this->makeListItem( $key, $item );
                  }
                ?>
              </ul>

            </li>

          <!-- </ul> -->
          <?php
        break;


        case 'VARIANTS':

          $theMsg = 'variants';
          $theData = $this->data['variant_urls'];
          ?>
          <?php if (count($theData) > 0) { ?>
            <ul class="nav" role="navigation">
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ): ?>
                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          <?php }

        break;

        case 'VIEWS':
          $theMsg = 'views';
          $theData = $this->data['view_urls'];
          ?>
          <?php if (count($theData) > 0) { ?>
            <ul class="nav" role="navigation">
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ): ?>
                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          <?php }
        break;


        case 'ACTIONS':

          $theMsg = 'actions';
          $theData = array_reverse($this->data['action_urls']);
          
          if (count($theData) > 0) {
            ?><!-- <ul class="nav" role="navigation"> -->
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><?php echo $this->msg( 'actions' ); ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ):

                    if (preg_match('/MovePage/', $link['href'])) {
                      echo '<li class="divider"></li>';
                    }

                    ?>

                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <!-- </ul> --><?php
          }

        break;


        case 'PERSONAL_LISTITEMS':
          $theMsg = 'personaltools';
          $theData = $this->getPersonalTools();
          $theTitle = $this->data['username'];

          foreach ( $theData as $key => $item ) {
            if ( !preg_match('/(notifications|login|createaccount)/', $key) ) {
              $showPersonal = true;
            }
          }

          ?>
          <!-- <ul class="nav navbar-nav navbar-right" role="navigation"> -->
            <li class="dropdown" id="p-notifications" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('notifications', $theData) ) {
              echo $this->makeListItem( 'notifications', $theData['notifications'] );
            } ?>
            </li>
            <?php if ( $wgLabstrapSkinLoginLocation == 'navbar' ): ?>
            <li class="dropdown" id="p-createaccount" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <?php if ( array_key_exists('createaccount', $theData) ) {
                echo $this->makeListItem( 'createaccount', $theData['createaccount'] );
              } ?>
            </li>
            <li class="dropdown" id="p-login" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('login', $theData) ) {
                echo $this->makeListItem( 'login', $theData['login'] );
            } ?>
            </li>
            <?php endif; ?>
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( !$showPersonal ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#">
                <i class="fa fa-user fa-lg"></i>
                <?php echo $theTitle; ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
              <?php foreach( $theData as $key => $item ) {

                if (preg_match('/preferences|logout/', $key)) {
                  echo '<li class="divider"></li>';
                } 
                // Do Show other user options
                // else if ( preg_match('/(notifications|login|createaccount)/', $key) ) {
                  // continue;
                // }

                echo $this->makeListItem( $key, $item );
              } ?>
              </ul>
            </li>
          <!-- </ul> -->
          <?php
        break;

        case 'PERSONAL_LISTITEMS_VISIBLE_XS':
          $theMsg = 'personaltools';
          $theData = $this->getPersonalTools();
          $theTitle = $this->data['username'];

          foreach ( $theData as $key => $item ) {
            if ( !preg_match('/(notifications|login|createaccount)/', $key) ) {
              $showPersonal = true;
            }
          }

          ?>
          <!-- <ul class="nav navbar-nav navbar-right" role="navigation"> -->
            <li class="dropdown visible-xs" id="p-notifications" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('notifications', $theData) ) {
              echo $this->makeListItem( 'notifications', $theData['notifications'] );
            } ?>
            </li>
            <?php if ( $wgLabstrapSkinLoginLocation == 'navbar' ): ?>
            <li class="dropdown visible-xs" id="p-createaccount" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <?php if ( array_key_exists('createaccount', $theData) ) {
                echo $this->makeListItem( 'createaccount', $theData['createaccount'] );
              } ?>
            </li>
            <li class="dropdown visible-xs" id="p-login" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('login', $theData) ) {
                echo $this->makeListItem( 'login', $theData['login'] );
            } ?>
            </li>
            <?php endif; ?>
            <li class="dropdown visible-xs" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( !$showPersonal ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#">
                <i class="fa fa-user fa-lg"></i>
                <?php echo $theTitle; ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
              <?php foreach( $theData as $key => $item ) {

                if (preg_match('/preferences|logout/', $key)) {
                  echo '<li class="divider"></li>';
                } 
                // Do Show other user options
                // else if ( preg_match('/(notifications|login|createaccount)/', $key) ) {
                  // continue;
                // }

                echo $this->makeListItem( $key, $item );
              } ?>
              </ul>
            </li>
          <!-- </ul> -->
          <?php
        break;


        case 'SEARCH':
          ?>
            <form class="navbar-form navbar-left" action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
              <div style="position: relative" class="form-group">
                <input id="searchInput" class="search-query form-control" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
                <button id="mw-searchButton" class="searchButton btn"><i class="fa fa-search"></i></button>
              </div>
            </form>

          <?php
        break;


        case 'SEARCHNAV':
          ?>
        <li>
          <a id="n-Search" class="search-link"><i class="icon-search"></i>Search</a>
          <form class="navbar-search" action="<?php $this->text( 'wgScript' ) ?>" id="nav-searchform">
                        <input id="nav-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
                        <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
          </form>
        </li>

          <?php
        break;


        case 'SEARCHFOOTER':
          ?>
            <form class="" action="<?php $this->text( 'wgScript' ) ?>" id="footer-search">
              <i class="icon-search"></i><b class="border"></b><input id="footer-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
              <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
            </form>

          <?php
        break;


        case 'SIDEBARNAV':
          foreach ( $this->data['sidebar'] as $name => $content ) {
            if ( !$content ) {
              continue;
            }
            if ( !in_array( $name, $wgLabstrapSkinSidebarItemsInNavbar ) ) {
                    continue;
            }
            $msgObj = wfMessage( $name );
            $name = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $name ); ?>
          <!-- <ul class="nav" role="navigation"> -->
          <li class="dropdown" id="p-<?php echo $name; ?>" class="vectorMenu">
          <a data-toggle="dropdown" class="dropdown-toggle" role="menu" href="#"><?php echo htmlspecialchars( $name ); ?> <b class="caret"></b></a>
          <ul aria-labelledby="<?php echo htmlspecialchars( $name ); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>><?php
            # This is a rather hacky way to name the nav.
            # (There are probably bugs here...) 
            foreach( $content as $key => $val ) {
              $navClasses = '';

              if (array_key_exists('view', $this->data['content_navigation']['views']) && $this->data['content_navigation']['views']['view']['href'] == $val['href']) {
                $navClasses = 'active';
              }?>

                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val); ?></li><?php
            }
          }?>
         </ul>
         </li>
         <!-- </ul> --><?php
        break;


        case 'SIDEBAR':
          $dropdownCategories = array('Wiki');

          foreach ( $this->data['sidebar'] as $name => $content ) {
            if ( !isset($content) ) {
              continue;
            }
            if ( in_array( $name, $wgLabstrapSkinSidebarItemsInNavbar ) ) {
                    continue;
            }
            $displayCategoryAsDropdown = in_array($name, $dropdownCategories);
            $msgObj = wfMessage( $name );
            $name = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $name );
            if ( $displayCategoryAsDropdown /*$wgLabstrapSkinDisplaySidebarNavigation*/ ) { ?>
              <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button" href="#"><?php echo htmlspecialchars( $name ); ?><b class="caret"></b></a>
                <ul aria-labelledby="<?php echo htmlspecialchars( $name ); ?>" role="menu" class="dropdown-menu"><?php
            }
            # This is a rather hacky way to name the nav.
            # (There are probably bugs here...) 
            foreach( $content as $key => $val ) {
              $navClasses = '';

              if (array_key_exists('view', $this->data['content_navigation']['views']) && $this->data['content_navigation']['views']['view']['href'] == $val['href']) {
                $navClasses = 'active';
              }?>

                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val); ?></li><?php
            }
            if ( $displayCategoryAsDropdown /*$wgLabstrapSkinDisplaySidebarNavigation*/ ) {?>                </ul>              </li><?php
            }          }
        break;

        case 'LANGUAGES':
          $theMsg = 'otherlanguages';
          $theData = $this->data['language_urls']; ?>
          <!-- <ul class="nav" role="navigation"> -->
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle brand" role="menu" href="#"><?php echo $this->html($theMsg) ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

              <?php foreach( $content as $key => $val ) { ?>
                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val, $options); ?></li><?php
              }?>

              </ul>            </li>
          <!-- </ul> --><?php
          break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }
}
