<h1 align="center">kobwise</h1>

<p align="center">Convert your Kobo annotations to Readwise highlights</p>

<p align="center">
    <img height="300" alt="Preview" src="/art/preview.png">
	<p align="center">
		<a href="https://github.com/osteel/kobwise/actions"><img alt="Build Status" src="https://github.com/osteel/kobwise/workflows/CI/badge.svg"></a>
		<a href="//packagist.org/packages/osteel/kobwise"><img alt="Latest Stable Version" src="https://poser.pugx.org/osteel/kobwise/v"></a>
		<a href="//packagist.org/packages/osteel/kobwise"><img alt="License" src="https://poser.pugx.org/osteel/kobwise/license"></a>
	</p>
</p>

## Why?

[Readwise](https://readwise.io) is an application allowing you to save highlights from books, articles, or any source of content you find inspiring and/or worth remembering. It will then resurface these highlights at a frequency of your choosing.

Readwise integrates with various services and platforms to ease the process of importing highlights, but unfortunately it is [not compatible with Kobo](https://help.readwise.io/article/82-does-readwise-support-kobo-highlights).

This small command-line application offers a bridge between the two, converting your Kobo annotations to a `.csv` file that is [compatible with Readwise](https://readwise.io/import_bulk).

## Install

kobwise is written in PHP and is installed using [Composer](https://getcomposer.org):

```
composer global require osteel/kobwise
```

Make sure the `~/.composer/vendor/bin` directory is in your system's "PATH".

## Use

All you need to do is call the `convert` command on the annotation file:

```
kobwise convert "The Almanack of Naval Ravikant.txt"
```

It will create a new `.csv` file in the current folder, which you can then [upload to Readwise](https://readwise.io/import_bulk).

## Known limitations

Kobo's annotation files are formatted in such a way that it is not easy to distinguish separate annotations from different paragraphs belonging to the same annotation.

It is therefore recommended to skim through the annotation file beforehand, and to delete any line break between paragraphs that should belong together.

As Readwise doesn't allow its users to [permanently delete highlights](https://help.readwise.io/article/123-why-cant-i-permanently-delete-highlights), it is also recommended to review the `.csv` file before import.

## Wait. I can export my Kobo annotations?

For some reason, Kobo does not enable this option by default, and you need to manually update a configuration file to do so.

The steps are the following:

1. Plug your Kobo to your computer
2. Open the `eReader.config` file at the root of your Kobo
3. Add this line at the very end: `[FeatureSettings] ExportHighlights=true`
4. Save the file and eject your Kobo
5. From your Kobo library, tap the three dots in front of the book you want to export annotations from
6. Tap `Export Annotations`
7. Connect your Kobo to your computer again
8. You should now see a `.txt` file at the root, containing the annotations

If you need more help, here is a [detailed guide](https://www.epubor.com/export-kobo-highlights-and-notes.html) with screenshots.