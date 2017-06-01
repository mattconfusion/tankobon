```
                             d8b                d8b                        
   d8P                       ?88                ?88                        
d888888P                      88b                88b     d888888P                 
  ?88'   d888b8b    88bd88b   888  d88' d8888b   888888b  d8888b   88bd88b 
  88P   d8P' ?88    88P' ?8b  888bd8P' d8P' ?88  88P `?8bd8P' ?88  88P' ?8b
  88b   88b  ,88b  d88   88P d88888b   88b  d88 d88,  d8888b  d88 d88   88P
  `?8b  `?88P'`88bd88'   88bd88' `?88b,`?8888P'd88'`?88P'`?8888P'd88'   88b
``` 

# Tankobon

**Tankobon** is a small php console app to package and organize in a single cbz file multiple separate folders of scanned media in order to read them on simple e-reader devices. **Tankobon** also optionally renames the image files using a numeric progression to ensure the correct folders order. 

## Installation

**Tankobon** relies on Composer for its dependencies. Be sure to run a composer update command in the **Tankobon** root folder. 

## Usage

*Tankobon* groups chapters in volumes in two ways:
- extracting the volume number from the chapter folder name (eg "Vol 01 - chapter 15"), which is the *volume* mode: you have to tell at which character the volume identifier starts and its length (in this case 0 and 6)
- scanning the source folders for chapter folders (already named and ordered correctly) and packaging volumes knowing how many chapter each volume holds, hence the name *chapter* mode 

You can give instructions to **Tankobon** by creating a *config.json* file somewhere on your disk.

Grouping chapters in *volume* mode (remove all comments from the json file!):
```
{
  "archive_prefix": "SeriesName", 
  "archive_suffix": "tankobon", 
  "grouping_mode": "volume", // mode for grouping, "volume" or "chapter"
  "rename_files_counter":"unique", //numeric progressive name for images, cross-folders
  "volume_mode": {
    "volume_number": {
      "string_start_index": "0", 
      "string_length": "6"
    }
  }
}
```
Grouping chapters in *chapter* mode:

```
{
  "archive_prefix": "test",
  "archive_suffix": "tankobon",
  "archive_extension": "cbz",
  "grouping_mode": "chapter",
  "rename_files_counter":"unique",
  "chapter_mode": {  
    "volumes": {
      "vol 1":8,
      "vol 2":9
      }
  
  }
}
```

Then use an ANSI console (it's for the cool colors, if you're using win you can checkout ANSIcon) and write this:
```
php path/to/tankobon/tankobon.php batch-process path/to/source/folders path/to/destination --config=path/to/config.json
```
optional flags like *--sanitize* and *--rename* allow you to sanitize filenames or renaming them using a progressive number.

## Credits

this messy code was written by Matteo Radice.

## TODO

- test this on other OS;
- improve Chapter grouping mode;
- implement split pages command: split double pages by specifying ltr or rtl direction;
- logging: write the console output to a log file;


## License

MIT License