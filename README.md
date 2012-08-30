#RSSFeed-Package

Collection of classes for working with RSS

##Example of usage
**in config.neon**

	services:
		Cache: Nette\Caching\Cache
		
		RSSFeed: Flame\Utils\RSSFeed
		TwitterRSSFeed:
			class: \Flame\Utils\TwitterRSSFeed
			autowired: no

		RSSFeedControlFactory: Portfolio\Components\RSSFeed\RSSFeedControlFactory
		TwitterRSSFeedControlFactory:
			class: Portfolio\Components\TwitterRSSFeed\TwitterRSSFeedControlFactory
			inject: no
			setup:
				- injectTwitterRSSFeed( @TwitterRSSFeed )
				- injectSettingFacade( @SettingFacade )