<?php

/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Item\Writer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Proxy Item.
 *
 * This interface contains the methods that you need to implement in your entity
 *
 * @author Rob Masters <mastahuk@gmail.com>
 */
class ProxyItem implements ItemInterface
{
    /**
     * @var RoutedItemInterface
     */
    protected $item;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param RoutedItemInterface $item
     * @param RouterInterface     $router
     */
    public function __construct(RoutedItemInterface $item, RouterInterface $router)
    {
        $this->item = $item;
        $this->router = $router;
    }

    /**
     * Returns item custom fields methods if exists in entity.
     *
     * @param string $method Method name
     * @param array  $args   Arguments array
     *
     * @throws \InvalidArgumentException If method is not defined
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this->item, $method)) {
            return call_user_func_array([$this->item, $method], $args);
        }

        throw new \InvalidArgumentException(sprintf('Method "%s" should be defined in your entity.', $method));
    }

    /**
     * This method returns feed item title.
     *
     * @return string
     */
    public function getFeedItemTitle()
    {
        return $this->item->getFeedItemTitle();
    }

    /**
     * This method returns feed item description (or content).
     *
     * @return string
     */
    public function getFeedItemDescription()
    {
        return $this->item->getFeedItemDescription();
    }

    /**
     * This method returns feed item URL link.
     *
     * @return string
     */
    public function getFeedItemLink()
    {
        $parameters = $this->item->getFeedItemRouteParameters() ?: [];

        $url = $this->router->generate($this->item->getFeedItemRouteName(), $parameters, UrlGeneratorInterface::ABSOLUTE_URL);

        $anchor = (string) $this->item->getFeedItemUrlAnchor();
        if ($anchor !== '') {
            $url .= '#'.$anchor;
        }

        return $url;
    }

    /**
     * This method returns item publication date.
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate()
    {
        return $this->item->getFeedItemPubDate();
    }
}
