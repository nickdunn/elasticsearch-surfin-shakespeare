# ElasticSearch Demo

This is a working demo of ElasticSearch integrated with Symphony using the [ElasticSearch extension](http://symphonyextensions.com/extensions/elasticsearch/). It contains the complete play-works of Shakespeare imported into four sections, primed and ready for indexing.

![Surfin' Shakespeare](http://nick-dunn.co.uk/assets/files/symphony/surfin-shakespeare.png)

## Install this demo

### 1. Clone repo and submodules
	
	git://github.com/nickdunn/elasticsearch-demo.git
	cd elasticsearch-demo
	git submodule init
	git submodule update

### 2. Set up database and config
Create a new database in MySQL and import the SQL dump in the root of this repo. Then rename `config.sample.php` to `config.php` and update your database connection details.

### 3. Configure ElasticSearch
Sign in to Symphony using user `test` and password `test`. Change that to something more memorable if you wish. All content has been imported into sections, so all that remains is to configure ElasticSearch itself.

1. Install ElasticSearch ([instructions](http://symphonyextensions.com/extensions/elasticsearch/#readme)) and verify it is running by receiving a JSON response at `http://localhost:9200/`.
2. Go to the System > Preferences page in Symphony and update the ElasticSearch connection details. Enter the above URL as the hostname, and enter whatever you wish as the database name (e.g. `shakespeare` or `testing`).
3. Save the Preferences page to create the new database index on the ElasticSearch instance. Use the `es-head` plugin to verify this has been created if you wish.
4. Visit the ElasticSearch > Mappings page in Symphony, choose all rows and select `Rebuild Mappings` from the With Selected menu. this will build the content type mappings for each section on the ElasticSearch Index.
5. Select all rows again and choose `Reindex Entries`. This will take some time, perhaps half an hour.

## Using the demo
The plays are broken down into four sections:

* Plays (the play title e.g. `Romeo and Juliet`)
* Acts (e.g. `Act IV`)
* Scenes (e.g. `Alexandria. A room in Cleopatra's palace`)
* Speeches (each individual speech by a character, contains the speaker name and their lines)


Visit `http://localhost/` (assuming you have set up the demo on `localhost`!) and you will see a search box. Search for your favourite play name or character (you _do_ have a favourite, don't you, Shakespeare enthusiast?).

Things to note:

The autocomplete uses a subpage `/search/suggest/` and matches play titles and speaker names only. This is configured in the section mapping JSON files — any field mapped with a sub-field named `symphony_autocomplete` is used in autocomplete.

Plays are boosted 3x above other entries so they always appear at the top of search results. This is configured in the section mapping PHP files. Changing this value requires the content to be re-indexed.

The extension has `build-entries` enabled so that the ElasticSearch data source returns both the excerpt text from ES and also all Symphony fields. This is used to supplement a result (e.g. a matched speech entry) with the speaker name and play title.

You can narrow your search to one section by clicking the section name on the left. In fact, you can multiply these together in the URL to demonstrate multi-section searching (e.g. `/search/?sections=speeches,plays`), although the page UI doesn't provide this functionality in this demo.

After playing with the demo for a while, check the log files in Symphony under ElasticSearch > Session Logs (analysis about per-user activity) and ElasticSearch > Query Logs (global keyword analysis) and think about how useful this might be in the real world. 