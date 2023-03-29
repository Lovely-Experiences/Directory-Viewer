<!DOCTYPE html>
<html lang="en">

<!-- 

    Directory Viewer
    https://github.com/Lovely-Experiences/Directory-Viewer

    Licensed under the Apache License 2.0

-->

<?php

/* --- SETTINGS/CONFIGURATION --- */

$path = './'; // Path to be viewed. It's important that you include the '/' at the end.
$recursive = true; // If true, child directories will be viewable as well.
$ignoredFiles = ['example']; // File extensions or the names of files that should be excluded.
$cssPath = 'index.css'; // Path to the css file.

$displayLink = true; // If true, the file name will also be a link to the file.
$displayLink_NewTab = false; // If true, file links will open in a new tab.
$displaySize = true; // If true, the size of the file is displayed.
$displayDownloadLink = true; // If true, the download link to a file is provided.
$displayModifiedTime = true; // If true, the date of which the file was last modified will be displayed.
$displayModifiedTime_Format = 'D, d M Y H:i:s'; // Format of which 'displayModifiedTime' is to be displayed in.

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
    <link rel="stylesheet" href="<?php echo $cssPath ?>">
</head>

<body>

    <?php
    if ($recursive and isset($_GET['folder'])) {
        $childFolder = htmlspecialchars($_GET['folder']);
        if ($childFolder)
            $path = $path . str_replace('..', '', $childFolder) . '/';
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
            <th>File</th>

            <?php
            if ($displaySize)
                echo '<th>Size</th>';
            if ($displayDownloadLink)
                echo '<th>Download</th>';
            if ($displayModifiedTime)
                echo '<th>Modified</th>';
            ?>

        </tr>

        <?php
        $files_Folders = [];
        $files_Normal = [];

        if (is_dir($path)) {
            foreach (scandir($path) as $file) {
                $filePath = $path . $file;
                if (is_dir($filePath)) {
                    $files_Folders[] = $file;
                } else {
                    $files_Normal[] = $file;
                }
            }
        }

        $files = array_merge($files_Folders, $files_Normal);
        foreach ($files as $file) {
            $filePath = $path . $file;
            if (is_dir($filePath)) {
                if (!in_array($file, $ignoredFiles)) {
                    echo '<tr><td>[folder] ' . $file . '</td></tr>';
                }
            } else {
                $fileExpanded = explode('.', $file);
                $fileExtension = end($fileExpanded);
                $fileName = reset($fileExpanded);
                if (!in_array($fileExtension, $ignoredFiles) and !in_array($fileName, $ignoredFiles)) {
                    echo '<tr>';
                    echo '<td>[file] ' . $file . '</td>';
                    if ($displaySize)
                        echo '<td>' . filesize($filePath) . ' bytes</td>';
                    if ($displayDownloadLink)
                        echo '<td><a href="' . $filePath . '" download>Download</a></td>';
                    if ($displayModifiedTime)
                        echo '<th>' . date($displayModifiedTime_Format, filemtime($filePath)) . '</th>';
                    echo '</tr>';
                }
            }
        }
        ?>

    </table>
</body>

</html>