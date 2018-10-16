CREATE TABLE `facebookuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(256) NOT NULL,
  `accesstoken` varchar(4096) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid_UNIQUE` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- This is a long lived access token. Expires in December 13th, 2018
INSERT into PullPrototype.facebookuser (userid,accesstoken,name) values 
('120932128882133', 'EAAKcvqehiQ0BAJPR9fZByfkWvqwYHA7GBZAJZA70Imz2ZAZCEZCqYXMuBAKfau1kawRK83kb0w0bzV7fmLZAK60l50wmZCLZADRqLYxpsi42APghuafaOtqw3ay2VAnqvdt3yQqtEFeD3jyaCnXRwaHZCq1qsCOwPq8ydKj8vnmlmeKwZDZD', 'Open Graph Test Use');
