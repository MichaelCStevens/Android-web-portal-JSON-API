<?php

class router {

    public $page;
    public $pageContent;
    public $pageTitle;
    private $siteName;
    public $pages;
    public $navMenu;
    public $noticeMsg;
    public static $fileRoot;
    public $theme;
    public $pubPages;
    public $data = array();
    public static $blacklistItems = array('ip', 'port', 'url', 'sms', 'phone', 'app', 'process', 'file', 'permissions', 'activities', 'receivers');

    function __construct() {
        session_start();
        if (!empty($_SESSION['notice'])) {
            $this->noticeMsg = $_SESSION['notice'];
            unset($_SESSION['notice']);
        }
        $this->blacklistItems = array('ip', 'port', 'url', 'sms', 'phone', 'app', 'process', 'file', 'permissions', 'activities', 'receivers');
        $this->siteName = 'Symantec - ';
        $this->pageTitle = "Mobile Data Analytics Dashboard DEMO";
        $this->fileRoot = 'C:/DWASFiles/Sites/androidtest101/VirtualDirectory0/site/wwwroot/';

        require_once router::$fileRoot . "classes/models/settings.model.php";

        $this->theme = settingsModel::getSettings('theme')->value;
        $this->pubPages = json_decode(settingsModel::getSettings('published_pages')->value);
        if (isset($_GET['view'])) {
            $this->page = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $this->page = 'index';
        }

        $this->buildMenu();
        $this->getPageContent();
    }

    public function getPageContent() {
        require_once router::$fileRoot . "classes/controllers/$this->page.controller.php";
        $c = $this->page . 'Controller';
        $this->controller = new $c;
        $this->model = $this->controller->model;

        if (isset($this->controller->data)) {
            extract($this->controller->data, EXTR_SKIP);
        }

        if (!in_array($this->page, $this->pubPages) && $this->page != 'index') {
            $this->redirectMessage('index.php', 'The page you are looking for has been disabled or does not exist');
        }

        switch ($this->page) {
            default:
                ob_start();
                require_once router::$fileRoot . "classes/views/index.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
            case "index":
                ob_start();
                require_once router::$fileRoot . "classes/views/index.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
            case "enterdata":
                ob_start();
                require_once router::$fileRoot . "classes/views/enterdata.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
            case "device":
                ob_start();
                require_once router::$fileRoot . "classes/views/device.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
            case "network":
                ob_start();
                require_once router::$fileRoot . "classes/views/network.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
            case "android":
                ob_start();
                require_once router::$fileRoot . "classes/views/android.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;

            case "settings":
                ob_start();
                require_once router::$fileRoot . "classes/views/settings.view.php";
                $this->pageContent = ob_get_contents();
                ob_end_clean();
                break;
        }
    }

    public function getPage() {
        return $this->page;
    }

    public function getSiteName() {
        return $this->siteName;
    }

    function validateUser($password) {
        // query mysql to authenticate user
        return false;
    }

    function buildPages() {
        $page = new stdClass();
        $page->url = 'index.php';
        $page->name = 'Home';
        $page->page = 'index';
        $this->pages[] = $page;
        unset($page);
        if (in_array('enterdata', $this->pubPages)) {
            $page = new stdClass();
            $page->url = 'index.php?view=enterdata';
            $page->name = 'Enter Data';
            $page->page = 'enterdata';
            $this->pages[] = $page;
            unset($page);
        }
        if (in_array('device', $this->pubPages)) {
            $page = new stdClass();
            $page->url = 'index.php?view=device';
            $page->name = 'Device Dashboard';
            $page->page = 'device';
            $this->pages[] = $page;
            unset($page);
        }
        if (in_array('network', $this->pubPages)) {
            $page = new stdClass();
            $page->url = 'index.php?view=network';
            $page->name = 'Network Dashboard';
            $page->page = 'network';
            $this->pages[] = $page;
            unset($page);
        }
        if (in_array('android', $this->pubPages)) {
            $page = new stdClass();
            $page->url = 'index.php?view=android';
            $page->name = 'Android Functions';
            $page->page = 'android';
            $this->pages[] = $page;
            unset($page);
        }

        $page = new stdClass();
        $page->url = 'index.php?view=settings';
        $page->name = 'Settings';
        $page->page = 'settings';
        $this->pages[] = $page;
        unset($page);
    }

    function buildMenu() {
        $this->buildPages();
        ob_start();
        echo"<ul>";
        foreach ($this->pages as $page) {
            if ($page->page == $this->page) {
                $class = 'active';
            } else {
                $class = '';
            }
            echo"<li class='$class'><a href='$page->url'>$page->name</a></li>";
        }
        echo"</ul>";
        $this->navMenu = ob_get_contents();
        ob_end_clean();
    }

    public static function redirectMessage($url, $msg) {
        $_SESSION['notice'] = $msg;
        header("Location: $url");
    }

}
