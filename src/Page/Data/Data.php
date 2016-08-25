<?php
namespace Frontend42\Page\Data;

use Frontend42\Model\Page;
use Frontend42\Model\Sitemap;
use Frontend42\Page\Data\Adapter\AdapterInterface;
use Frontend42\Page\Data\Storage\StorageInterface;
use Frontend42\PageType\PageContent\PageContent;
use Psr\Cache\CacheItemPoolInterface;
use Zend\Stdlib\PriorityList;

class Data implements StorageInterface, AdapterInterface
{
    /**
     * @var PriorityList
     */
    protected $adapter;

    /**
     * @var PriorityList
     */
    protected $storage;

    /**
     * Data constructor.
     */
    public function __construct()
    {
        $this->storage = new PriorityList();
        $this->adapter = new PriorityList();
    }

    /**
     * @param StorageInterface $storage
     * @param int $priority
     */
    public function addStorage(StorageInterface $storage, $priority = 0)
    {
        $this->storage->insert(get_class($storage), $storage, $priority);
    }

    /**
     * @param StorageInterface|string $name
     */
    public function removeStorage($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }

        $this->storage->remove($name);
    }

    /**
     * @param AdapterInterface $adapter
     * @param int $priority
     */
    public function addAdapter(AdapterInterface $adapter, $priority = 0)
    {
        $this->adapter->insert(get_class($adapter), $adapter, $priority);
    }

    /**
     * @param AdapterInterface|string $name
     */
    public function removeAdapter($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }

        $this->adapter->remove($name);
    }

    /**
     * @param array $routing
     */
    public function writeRouting(array $routing)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writeRouting($routing);
        }
    }

    /**
     * @param array $navigation
     * @param $locale
     */
    public function writeNavigation(array $navigation, $locale)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writeNavigation($navigation, $locale);
        }
    }

    /**
     * @param Page $page
     */
    public function writePage(Page $page)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writePage($page);
        }
    }

    /**
     * @param Sitemap $sitemap
     */
    public function writeSitemap(Sitemap $sitemap)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writeSitemap($sitemap);
        }
    }

    /**
     * @param int $pageId
     * @param string $route
     */
    public function writePageRoute($pageId, $route)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writePageRoute($pageId, $route);
        }
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @param int $pageId
     */
    public function writeLocaleMapping($sitemapId, $locale, $pageId)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writeLocaleMapping($sitemapId, $locale, $pageId);
        }
    }

    /**
     * @param string $handle
     * @param string $locale
     * @param int $pageId
     */
    public function writeHandleMapping($handle, $locale, $pageId)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writeHandleMapping($handle, $locale, $pageId);
        }
    }

    /**
     * @param string $versionId
     * @param int $pageId
     * @param PageContent $content
     */
    public function writePageContent($versionId, $pageId, PageContent $content)
    {
        /** @var StorageInterface $storage */
        foreach ($this->storage as $storage) {
            $storage->writePageContent($versionId, $pageId, $content);
        }
    }


    /**
     * @return array
     */
    public function getRouting()
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $routing = $adapter->getRouting();
            if ($routing === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writeRouting($routing);
            }

            return $routing;
        }
    }

    /**
     * @param $locale
     * @return array
     */
    public function getNavigation($locale)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $navigation = $adapter->getNavigation($locale);
            if ($navigation === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writeNavigation($navigation, $locale);
            }

            return $navigation;
        }
    }

    /**
     * @param int $pageId
     * @return Page|null
     */
    public function getPage($pageId)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $page = $adapter->getPage($pageId);
            if ($page === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writePage($page);
            }

            return $page;
        }
    }

    /**
     * @param int $sitemapId
     * @return Sitemap|null
     */
    public function getSitemap($sitemapId)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $sitemap = $adapter->getSitemap($sitemapId);
            if ($sitemap === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writeSitemap($sitemap);
            }

            return $sitemap;
        }
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return null|int
     */
    public function getHandleMapping($handle, $locale)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $pageId = $adapter->getHandleMapping($handle, $locale);
            if ($pageId === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writeHandleMapping($handle, $locale, $pageId);
            }

            return $pageId;
        }
    }

    /**
     * @param int $sitemapId
     * @param string $locale
     * @return int|null
     */
    public function getLocaleMapping($sitemapId, $locale)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $pageId = $adapter->getLocaleMapping($sitemapId, $locale);
            if ($pageId === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }
                continue;
            }

            if ($miss === true) {
                $this->writeLocaleMapping($sitemapId, $locale, $pageId);
            }

            return $pageId;
        }
    }

    /**
     * @param $pageId
     * @return null|string
     */
    public function getPageRoute($pageId)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $route = $adapter->getPageRoute($pageId);
            if ($route === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }

                continue;
            }

            if ($miss === true) {
                $this->writePageRoute($pageId, $route);
            }

            return $route;
        }
    }

    /**
     * @param mixed $versionId
     * @param int $pageId
     * @return PageContent
     */
    public function getPageContent($versionId, $pageId)
    {
        $miss = false;
        /** @var AdapterInterface $adapter */
        foreach ($this->adapter as $adapter) {
            $pageContent = $adapter->getPageContent($versionId, $pageId);
            if ($pageContent === null) {
                if ($adapter->canMiss() == false) {
                    $miss = true;
                }

                continue;
            }

            if ($miss === true) {
                $this->writePageContent($versionId, $pageId, $pageContent);
            }

            return $pageContent;
        }
    }


    /**
     * @return boolean
     */
    public function canMiss()
    {
        return false;
    }
}
