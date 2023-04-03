<!DOCTYPE html>
<html lang="en">

<!-- 

    Directory Viewer
    https://github.com/Lovely-Experiences/Directory-Viewer

    Licensed under the Apache License 2.0

-->

<?php

/* --- SETTINGS/CONFIGURATION --- */

$path = '../../'; // Path to be viewed. It's important that you include the '/' at the end.
$recursive = true; // If true, child directories will be viewable as well. This will add clickable a view icon next to each folder icon.
$ignoredFiles = ['.', '..']; // File extensions or the names of files/folders that should be excluded.
$cssPath = 'index.css'; // Path to the css file.

$displayLink = true; // If true, the file/folder name will also be a link to the file/folder.
$displayLink_NewTab = false; // If true, file/folder links will open in a new tab.
$displaySize = true; // If true, the size of files will be displayed.
$displayDownloadLink = true; // If true, the download link to each file is provided.
$displayModifiedTime = true; // If true, the date of which each file was last modified will be displayed.
$displayModifiedTime_Format = 'D, d M Y H:i:s'; // Format of which 'displayModifiedTime' is to be displayed in.
$displayDifferentFileIcons = true; // If true, different file types will have different icons.
$displayFileView = true; // If true, a clickable icon to display the items content in a code-like view will be available for each file.

$mobileMode = true; // If true, small mobile devices will only include the files/folders and download (if enabled) sections.
$oldPHPSupport = true; // If true, some features may be removed or limited to support older versions of PHP. This should be considered experimental.

// Please remove the following 4 lines if you are using this in production.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
clearstatcache();

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory Viewer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.7.0/build/styles/default.min.css">
    <link rel="stylesheet" href="<?php echo $cssPath ?>">

    <?php
    if ($mobileMode) {
        echo '<style>';
        echo '@media screen and (max-width: 650px) { .file-size-content, .file-last-modified-content { display: none; } }';
        echo '</style>';
    }
    ?>

</head>

<body>

    <?php
    $currentlyNotOnMain = false;
    $currentPreviewFolder = null;
    if ($recursive and isset($_GET['f'])) {
        $childFolder = htmlspecialchars($_GET['f']);
        $currentPreviewFolder = $childFolder;
        if (!$oldPHPSupport) {
            if (str_contains($childFolder, '..')) {
                echo '<h1>Path not allowed.</h1>';
                echo '</body>';
                exit();
            }
        }
        if ($childFolder) {
            $path = $path . str_replace('..', '', $childFolder) . '/';
            $currentlyNotOnMain = true;
        }
        if (!is_dir($path)) {
            echo '<h1>Invalid directory.</h1>';
            echo '</body>';
            exit();
        }
    }
    ?>

    <h1>Directory Files</h1>

    <p>
        Viewing the contents of
        <b>
            <a href="<?php echo $path ?>"><?php echo $path ?></a>
        </b>
    </p>

    <table>
        <tr>
            <th>Folders</th>
        </tr>

        <?php
        if ($currentlyNotOnMain) {
            echo '<tr><td><a href="javascript:history.back()"><span class="material-icons-round">arrow_back_ios</span>Previous</a></td></tr>';
        }

        $files_Folders = [];
        $files_Normal = [];

        foreach (scandir($path) as $file) {
            $filePath = $path . $file;
            if (is_dir($filePath)) {
                $files_Folders[] = $file;
            } else {
                $files_Normal[] = $file;
            }
        }

        $alreadyCreatedFileHeaders = false;
        $noVisibleFolders = true;
        $files = array_merge($files_Folders, $files_Normal);
        foreach ($files as $file) {
            $filePath = $path . $file;
            if (is_dir($filePath)) {
                if (!in_array($file, $ignoredFiles)) {
                    $noVisibleFolders = false;
                    echo '<tr><td>';
                    if ($recursive) {
                        if ($currentPreviewFolder) {
                            echo '<a href="./?f=' . $currentPreviewFolder . '/' . $file . '"><span class="material-icons-round">visibility</span></a>';
                        } else {
                            echo '<a href="./?f=' . $file . '"><span class="material-icons-round">visibility</span></a>';
                        }
                    }
                    echo '<span class="material-icons-round">folder</span> ';
                    if ($displayLink) {
                        echo '<a href="' . $path . $file . '" ';
                        if ($displayLink_NewTab) {
                            echo 'target="_blank"';
                        }
                        echo ">" . $file . "</a>";
                    } else {
                        echo $file;
                    }
                    echo '</td></tr>';
                }
            } else {
                if (!$alreadyCreatedFileHeaders) {
                    if ($noVisibleFolders) {
                        echo '<tr><td><span class="material-icons-round">close</span> No Folders</td></tr>';
                        $noVisibleFolders = false;
                    }
                    echo '<tr>';
                    echo '<th>Files</th>';
                    if ($displaySize)
                        echo '<th class="file-size-content">Size</th>';
                    if ($displayDownloadLink)
                        echo '<th>Download</th>';
                    if ($displayModifiedTime)
                        echo '<th class="file-last-modified-content">Modified</th>';
                    echo '</tr>';
                    $alreadyCreatedFileHeaders = true;
                }
                $fileExpanded = explode('.', $file);
                $fileExtension = end($fileExpanded);
                $fileName = reset($fileExpanded);
                if (!in_array($fileExtension, $ignoredFiles) and !in_array($fileName, $ignoredFiles)) {
                    $fileIcon = 'description';
                    if ($displayDifferentFileIcons) {
                        $e = $fileExtension;
                        if ($e == 'txt' or $e == 'md')
                            $fileIcon = 'article';
                        if ($e == 'html' or $e == 'php' or $e == 'css' or $e == 'js')
                            $fileIcon = 'code';
                        if ($e == 'zip')
                            $fileIcon = 'folder_zip';
                        if ($e == 'png' or $e == 'jpg' or $e == 'jpeg' or $e == 'svg' or $e == 'webp')
                            $fileIcon = 'image';
                        if ($e == 'ttf' or $e == 'otf')
                            $fileIcon = 'text_fields';
                    }
                    echo '<tr><td>';
                    if ($displayFileView) {
                        echo '<a class="file-view" data-url="' . $path . $file . '" data-type="' . $fileExtension . '"><span class="material-icons-round">visibility</span></a>';
                    }
                    echo '<span class="material-icons-round">' . $fileIcon . '</span> ';
                    if ($displayLink) {
                        echo '<a href="' . $path . $file . '" ';
                        if ($displayLink_NewTab) {
                            echo 'target="_blank"';
                        }
                        echo ">" . $file . "</a>";
                    } else {
                        echo $file;
                    }
                    echo '</td>';
                    if ($displaySize)
                        try {
                            echo '<td class="file-size-content">' . filesize($filePath) . ' bytes</td>';
                        } catch (Exception $error) {
                            echo '<td class="file-size-content">' . 'Failed to fetch.' . '</td>';
                        }
                    if ($displayDownloadLink)
                        echo '<td><a href="' . $filePath . '" download>Download</a></td>';
                    if ($displayModifiedTime)
                        try {
                            echo '<td class="file-last-modified-content">' . date($displayModifiedTime_Format, filemtime($filePath)) . '</td>';
                        } catch (Exception $error) {
                            echo '<td class="file-last-modified-content">' . 'Failed to fetch.' . '</td>';
                        }
                    echo '</tr>';
                }
            }
        }
        if ($noVisibleFolders) {
            echo '<tr><td><span class="material-icons-round">close</span> No Folders or Files</td></tr>';
        }
        ?>

    </table>
    <div id="code-viewer-background">
        <button id="exit-code-viewer">
        Exit Code Viewer
        </button>
        <div id="code-viewer" class="hljs">

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/showdown@2.1.0/dist/showdown.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.7.0/build/highlight.min.js"></script>
    <script>
        /*
    
            Directory Viewer
            https://github.com/Lovely-Experiences/Directory-Viewer
    
            Licensed under the Apache License 2.0
    
        */

        window.onload = function () {

            const fileViewerEnabled = new Boolean(<?php echo $displayFileView ?>);
            if (fileViewerEnabled) {

                const codeViewerBackground = document.getElementById('code-viewer-background');
                const codeViewer = document.getElementById('code-viewer');
                const codeViewerExit = document.getElementById('exit-code-viewer');

                function update(code, lang) {
                    if (hljs.getLanguage(lang) === undefined) {
                        codeViewerBackground.style.display = "block";
                        codeViewer.innerHTML = "Unable to view.";
                        return;
                    }
                    const result = hljs.highlight(code, { language: lang, ignoreIllegals: true });
                    codeViewerBackground.style.display = "block";
                    if (result.value) {
                        codeViewer.innerHTML = result.value;
                    } else {
                        codeViewer.innerHTML = "Unable to view.";
                    }
                }

                codeViewerExit.onclick = function () {
                    codeViewerBackground.style.display = 'none';
                };

                const elements = document.getElementsByClassName('file-view');
                for (let i = 0; i < elements.length; i++) {
                    const element = elements[i];
                    element.onclick = function() {
                        const url = element.dataset.url;
                        const type = element.dataset.type;
                        update('Loading...', 'txt');
                        fetch(url).then(async function(result) {
                            const text = await result.text();
                            update(text, type);
                        }).catch(function(err) {
                            update(`Failed to load. ${err}`, 'txt');
                        });
                    }
                }

            }

        }
    </script>
</body>

</html>