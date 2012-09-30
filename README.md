#RSSFeed-Package

Collection of classes for working with RSS

##Example of usage
**in config.neon**

	services:
		Cache: Nette\Caching\Cache

		RSSReader: \Flame\Packages\RSSFeed\RSSReaderService
        TwitterRSSReader: \Flame\Packages\RSSFeed\TwitterRSSReaderService