The Mahara External Resource artefact allows you to add embedable content (videos, pictures, ...) and resources (pdf, rss, etc) to your Mahara portfolio. To some extent it supersedes the standard "external media" block that comes with Mahara by supporting more sources. All providers that implement any of the protocols (oembed, ogp, ...) is supported - [Youtube](http://www.youtube.com), [Dailymotion](http://www.dailymotion.com), [Slideshare](http://www.slideshare.net/), [Google Books](http://books.google.fr/), [Scratch](http://scratch.mit.edu/) and more.

### Printscreen ###
![http://mahara-artefact-external-resource.googlecode.com/hg/theme/raw/static/images/printscreen.png](http://mahara-artefact-external-resource.googlecode.com/hg/theme/raw/static/images/printscreen.png)

### How to add a resource ###
Integration of content is done by copying and pasting the page's urls to a block. Embeded content such as videos, pictures, articles is extracted from the page and displayed in the block.

### How it is done ###
The artefact works by leveraging several standards to extract the content from the resource. Most noticeably it makes use of

  * oembed: http://oembed.com/
  * open graph protocol: http://ogp.me/

to extract content.
### Supported providers ###
All content providers that implement any one of the protocols is supported. As example here is a small list of supported providers and content

Youtube, Dailymotion, Slideshare, Google Books, Flikr, Tsr, Rss, pdf, doc, ...

### Get the code ###
To get the latest code you need to clone the repository using a mercurial client. Windows users can use TortoiseHG http://tortoisehg.bitbucket.org/ for that. See the Source tab for more details.

Or you can download a release from the Downloads tab.

### Install ###
Copy the code in Mahara to

> mahara/artefact/extresource

Install you artefact as usual

  1. Go to Mahara->administration->Extentions
  1. Install the artefact
  1. Install the corresponding block