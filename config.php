<?php

function getFileType($filename)
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}
