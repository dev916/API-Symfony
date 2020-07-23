<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appDevUrlMatcher.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appDevUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        if (0 === strpos($pathinfo, '/_')) {
            // _wdt
            if (0 === strpos($pathinfo, '/_wdt') && preg_match('#^/_wdt/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => '_wdt')), array (  '_controller' => 'web_profiler.controller.profiler:toolbarAction',));
            }

            if (0 === strpos($pathinfo, '/_profiler')) {
                // _profiler_home
                if (rtrim($pathinfo, '/') === '/_profiler') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_profiler_home');
                    }

                    return array (  '_controller' => 'web_profiler.controller.profiler:homeAction',  '_route' => '_profiler_home',);
                }

                if (0 === strpos($pathinfo, '/_profiler/search')) {
                    // _profiler_search
                    if ($pathinfo === '/_profiler/search') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchAction',  '_route' => '_profiler_search',);
                    }

                    // _profiler_search_bar
                    if ($pathinfo === '/_profiler/search_bar') {
                        return array (  '_controller' => 'web_profiler.controller.profiler:searchBarAction',  '_route' => '_profiler_search_bar',);
                    }

                }

                // _profiler_purge
                if ($pathinfo === '/_profiler/purge') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:purgeAction',  '_route' => '_profiler_purge',);
                }

                // _profiler_info
                if (0 === strpos($pathinfo, '/_profiler/info') && preg_match('#^/_profiler/info/(?P<about>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_info')), array (  '_controller' => 'web_profiler.controller.profiler:infoAction',));
                }

                // _profiler_phpinfo
                if ($pathinfo === '/_profiler/phpinfo') {
                    return array (  '_controller' => 'web_profiler.controller.profiler:phpinfoAction',  '_route' => '_profiler_phpinfo',);
                }

                // _profiler_search_results
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/search/results$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_search_results')), array (  '_controller' => 'web_profiler.controller.profiler:searchResultsAction',));
                }

                // _profiler
                if (preg_match('#^/_profiler/(?P<token>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler')), array (  '_controller' => 'web_profiler.controller.profiler:panelAction',));
                }

                // _profiler_router
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/router$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_router')), array (  '_controller' => 'web_profiler.controller.router:panelAction',));
                }

                // _profiler_exception
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception')), array (  '_controller' => 'web_profiler.controller.exception:showAction',));
                }

                // _profiler_exception_css
                if (preg_match('#^/_profiler/(?P<token>[^/]++)/exception\\.css$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_profiler_exception_css')), array (  '_controller' => 'web_profiler.controller.exception:cssAction',));
                }

            }

            if (0 === strpos($pathinfo, '/_configurator')) {
                // _configurator_home
                if (rtrim($pathinfo, '/') === '/_configurator') {
                    if (substr($pathinfo, -1) !== '/') {
                        return $this->redirect($pathinfo.'/', '_configurator_home');
                    }

                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::checkAction',  '_route' => '_configurator_home',);
                }

                // _configurator_step
                if (0 === strpos($pathinfo, '/_configurator/step') && preg_match('#^/_configurator/step/(?P<index>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_configurator_step')), array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::stepAction',));
                }

                // _configurator_final
                if ($pathinfo === '/_configurator/final') {
                    return array (  '_controller' => 'Sensio\\Bundle\\DistributionBundle\\Controller\\ConfiguratorController::finalAction',  '_route' => '_configurator_final',);
                }

            }

        }

        // _demo
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_demo');
            }

            return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\DemoController::loginAction',  '_route' => '_demo',);
        }

        if (0 === strpos($pathinfo, '/demo/secured')) {
            if (0 === strpos($pathinfo, '/demo/secured/log')) {
                if (0 === strpos($pathinfo, '/demo/secured/login')) {
                    // _demo_login
                    if ($pathinfo === '/demo/secured/login') {
                        return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::loginAction',  '_route' => '_demo_login',);
                    }

                    // _security_check
                    if ($pathinfo === '/demo/secured/login_check') {
                        return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::securityCheckAction',  '_route' => '_security_check',);
                    }

                }

                // _demo_logout
                if ($pathinfo === '/demo/secured/logout') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::logoutAction',  '_route' => '_demo_logout',);
                }

            }

            if (0 === strpos($pathinfo, '/demo/secured/hello')) {
                // acme_demo_secured_hello
                if ($pathinfo === '/demo/secured/hello') {
                    return array (  'name' => 'World',  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',  '_route' => 'acme_demo_secured_hello',);
                }

                // _demo_secured_hello
                if (preg_match('#^/demo/secured/hello/(?P<name>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_demo_secured_hello')), array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloAction',));
                }

                // _demo_secured_hello_admin
                if (0 === strpos($pathinfo, '/demo/secured/hello/admin') && preg_match('#^/demo/secured/hello/admin/(?P<name>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => '_demo_secured_hello_admin')), array (  '_controller' => 'Acme\\DemoBundle\\Controller\\SecuredController::helloadminAction',));
                }

            }

        }

        if (0 === strpos($pathinfo, '/webservice')) {
            // _webservice
            if (rtrim($pathinfo, '/') === '/webservice') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', '_webservice');
                }

                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::indexAction',  '_route' => '_webservice',);
            }

            // _login
            if ($pathinfo === '/webservice/login') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::loginAction',  '_route' => '_login',);
            }

            if (0 === strpos($pathinfo, '/webservice/s')) {
                // _signup
                if ($pathinfo === '/webservice/signup') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::signupAction',  '_route' => '_signup',);
                }

                // _spregistration
                if ($pathinfo === '/webservice/spregistration') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::spregistrationAction',  '_route' => '_spregistration',);
                }

            }

            // _forgot
            if ($pathinfo === '/webservice/forgot') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::forgotAction',  '_route' => '_forgot',);
            }

            if (0 === strpos($pathinfo, '/webservice/s')) {
                // _sociallogin
                if ($pathinfo === '/webservice/sociallogin') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::socialloginAction',  '_route' => '_sociallogin',);
                }

                // _services
                if ($pathinfo === '/webservice/services') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::servicesAction',  '_route' => '_services',);
                }

            }

            // _category
            if ($pathinfo === '/webservice/category') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::categoryAction',  '_route' => '_category',);
            }

            // _profileupdate
            if ($pathinfo === '/webservice/profileupdate') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::profileupdateAction',  '_route' => '_profileupdate',);
            }

            // _userservices
            if ($pathinfo === '/webservice/userservices') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::userservicesAction',  '_route' => '_userservices',);
            }

            if (0 === strpos($pathinfo, '/webservice/s')) {
                // _signupdoubleemail
                if ($pathinfo === '/webservice/signupdoubleemail') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::signupdoubleemailAction',  '_route' => '_signupdoubleemail',);
                }

                // _spprofiledetail
                if ($pathinfo === '/webservice/spprofiledetail') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::spprofiledetailAction',  '_route' => '_spprofiledetail',);
                }

            }

            // _customers
            if ($pathinfo === '/webservice/customers') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::customerAction',  '_route' => '_customers',);
            }

            // _follow
            if ($pathinfo === '/webservice/follow') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::followAction',  '_route' => '_follow',);
            }

            // _consumerprofile
            if ($pathinfo === '/webservice/consumerprofile') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::consumerprofileAction',  '_route' => '_consumerprofile',);
            }

            if (0 === strpos($pathinfo, '/webservice/s')) {
                // _serviceprovider
                if ($pathinfo === '/webservice/serviceprovider') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::serviceproviderAction',  '_route' => '_serviceprovider',);
                }

                // _spconsumerregistration
                if ($pathinfo === '/webservice/spconsumerregistration') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::spconsumerregistrationAction',  '_route' => '_spconsumerregistration',);
                }

            }

            if (0 === strpos($pathinfo, '/webservice/my')) {
                // _mycustomer
                if ($pathinfo === '/webservice/mycustomer') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::mycustomerAction',  '_route' => '_mycustomer',);
                }

                // _myservices
                if ($pathinfo === '/webservice/myservices') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::myservicesAction',  '_route' => '_myservices',);
                }

            }

            // _serviceProviderprofileinfo
            if ($pathinfo === '/webservice/serviceProviderprofileinfo') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::serviceProviderprofileinfoAction',  '_route' => '_serviceProviderprofileinfo',);
            }

            if (0 === strpos($pathinfo, '/webservice/con')) {
                // _consumerprofileinfo
                if ($pathinfo === '/webservice/consumerprofileinfo') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::consumerprofileinfoAction',  '_route' => '_consumerprofileinfo',);
                }

                // _contactinfo
                if ($pathinfo === '/webservice/contactinfo') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::contactinfoAction',  '_route' => '_contactinfo',);
                }

            }

            // _latlong
            if ($pathinfo === '/webservice/latlong') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::latlongAction',  '_route' => '_latlong',);
            }

            if (0 === strpos($pathinfo, '/webservice/imageup')) {
                // _imageupload
                if ($pathinfo === '/webservice/imageupload') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::imageuploadAction',  '_route' => '_imageupload',);
                }

                // _imageupdate
                if ($pathinfo === '/webservice/imageupdate') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::imageupdateAction',  '_route' => '_imageupdate',);
                }

            }

            // _consumerloginwithpic
            if ($pathinfo === '/webservice/consumerloginwithpic') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::consumerloginwithpicAction',  '_route' => '_consumerloginwithpic',);
            }

            // _useralbum
            if ($pathinfo === '/webservice/useralbum') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::useralbumAction',  '_route' => '_useralbum',);
            }

            if (0 === strpos($pathinfo, '/webservice/album')) {
                // _albumstatus
                if ($pathinfo === '/webservice/albumstatus') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::albumstatusAction',  '_route' => '_albumstatus',);
                }

                // _albumimages
                if ($pathinfo === '/webservice/albumimages') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::albumimagesAction',  '_route' => '_albumimages',);
                }

            }

            // _checkpost
            if ($pathinfo === '/webservice/checkpost') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::checkpostAction',  '_route' => '_checkpost',);
            }

            if (0 === strpos($pathinfo, '/webservice/userr')) {
                // _userrate
                if ($pathinfo === '/webservice/userrate') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::userrateAction',  '_route' => '_userrate',);
                }

                // _userreviewsrate
                if ($pathinfo === '/webservice/userreviewsrate') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::userreviewsrateAction',  '_route' => '_userreviewsrate',);
                }

            }

            // _mostusedtags
            if ($pathinfo === '/webservice/mostusedtags') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::mostusedtagsAction',  '_route' => '_mostusedtags',);
            }

            // _termscondition
            if ($pathinfo === '/webservice/termscondition') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::termsconditionAction',  '_route' => '_termscondition',);
            }

            // _aboutus
            if ($pathinfo === '/webservice/aboutus') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::aboutusAction',  '_route' => '_aboutus',);
            }

            // _privacypolicy
            if ($pathinfo === '/webservice/privacypolicy') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::privacypolicyAction',  '_route' => '_privacypolicy',);
            }

            if (0 === strpos($pathinfo, '/webservice/re')) {
                // _reportproblem
                if ($pathinfo === '/webservice/reportproblem') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::reportproblemAction',  '_route' => '_reportproblem',);
                }

                // _resendpassword
                if ($pathinfo === '/webservice/resendpassword') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::resendpasswordAction',  '_route' => '_resendpassword',);
                }

            }

            // _changepassword
            if ($pathinfo === '/webservice/changepassword') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::changepasswordAction',  '_route' => '_changepassword',);
            }

            // _birthdaynotification
            if ($pathinfo === '/webservice/birthdaynotification') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::birthdaynotificationAction',  '_route' => '_birthdaynotification',);
            }

            // _search
            if ($pathinfo === '/webservice/search') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::searchAction',  '_route' => '_search',);
            }

            // _trending
            if ($pathinfo === '/webservice/trending') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::trendingAction',  '_route' => '_trending',);
            }

            // _suggested
            if ($pathinfo === '/webservice/suggested') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::suggestedAction',  '_route' => '_suggested',);
            }

            if (0 === strpos($pathinfo, '/webservice/viewall')) {
                // _viewallservices
                if ($pathinfo === '/webservice/viewallservices') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::viewallservicesAction',  '_route' => '_viewallservices',);
                }

                // _viewallcustomer
                if ($pathinfo === '/webservice/viewallcustomer') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::viewallcustomerAction',  '_route' => '_viewallcustomer',);
                }

            }

            // _local
            if ($pathinfo === '/webservice/local') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::localAction',  '_route' => '_local',);
            }

            if (0 === strpos($pathinfo, '/webservice/follow')) {
                // _follower
                if ($pathinfo === '/webservice/follower') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::followerAction',  '_route' => '_follower',);
                }

                // _following
                if ($pathinfo === '/webservice/following') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::followingAction',  '_route' => '_following',);
                }

            }

            // _sploginwithpic
            if ($pathinfo === '/webservice/sploginwithpic') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::sploginwithpicAction',  '_route' => '_sploginwithpic',);
            }

            // _logout
            if ($pathinfo === '/webservice/logout') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::logoutAction',  '_route' => '_logout',);
            }

            // _servicenotification
            if ($pathinfo === '/webservice/servicenotification') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::servicenotificationAction',  '_route' => '_servicenotification',);
            }

            // _notificationlisting
            if ($pathinfo === '/webservice/notificationlisting') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::notificationlistingAction',  '_route' => '_notificationlisting',);
            }

            // _counter
            if ($pathinfo === '/webservice/counter') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::counterAction',  '_route' => '_counter',);
            }

            // _notificationstatus
            if ($pathinfo === '/webservice/notificationstatus') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::notificationstatusAction',  '_route' => '_notificationstatus',);
            }

            if (0 === strpos($pathinfo, '/webservice/chat')) {
                // _chating
                if ($pathinfo === '/webservice/chating') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::chatingAction',  '_route' => '_chating',);
                }

                // _chatdelete
                if ($pathinfo === '/webservice/chatdelete') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::chatdeleteAction',  '_route' => '_chatdelete',);
                }

                // _chatlisting
                if ($pathinfo === '/webservice/chatlisting') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::chatlistingAction',  '_route' => '_chatlisting',);
                }

            }

            // _notificationsetting
            if ($pathinfo === '/webservice/notificationsetting') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::notificationsettingAction',  '_route' => '_notificationsetting',);
            }

            // _followerfollowing
            if ($pathinfo === '/webservice/followerfollowing') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::followerfollowingAction',  '_route' => '_followerfollowing',);
            }

            // _ontaglisting
            if ($pathinfo === '/webservice/ontaglisting') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::ontaglistingAction',  '_route' => '_ontaglisting',);
            }

            // _customerreviews
            if ($pathinfo === '/webservice/customerreviews') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::customerreviewsAction',  '_route' => '_customerreviews',);
            }

            // _editalbum
            if ($pathinfo === '/webservice/editalbum') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::editalbumAction',  '_route' => '_editalbum',);
            }

            // _systeminfo
            if ($pathinfo === '/webservice/systeminfo') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::systeminfoAction',  '_route' => '_systeminfo',);
            }

            if (0 === strpos($pathinfo, '/webservice/d')) {
                // _domain
                if ($pathinfo === '/webservice/domain') {
                    return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::domainAction',  '_route' => '_domain',);
                }

                if (0 === strpos($pathinfo, '/webservice/delete')) {
                    // _deleteimage
                    if ($pathinfo === '/webservice/deleteimage') {
                        return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::deleteimageAction',  '_route' => '_deleteimage',);
                    }

                    // _deletepictureset
                    if ($pathinfo === '/webservice/deletepictureset') {
                        return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::deletepicturesetAction',  '_route' => '_deletepictureset',);
                    }

                }

            }

            // _userchatlist
            if ($pathinfo === '/webservice/userchatlist') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::userchatlistAction',  '_route' => '_userchatlist',);
            }

            // _spalbumstatus
            if ($pathinfo === '/webservice/spalbumstatus') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::spalbumstatusAction',  '_route' => '_spalbumstatus',);
            }

            // _custuseralbum
            if ($pathinfo === '/webservice/custuseralbum') {
                return array (  '_controller' => 'Acme\\DemoBundle\\Controller\\ApiController::custuseralbumAction',  '_route' => '_custuseralbum',);
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
