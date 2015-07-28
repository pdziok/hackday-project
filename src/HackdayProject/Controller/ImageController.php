<?php
/**
 * @author pdziok
 */
namespace HackdayProject\Controller;

use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    public function postImageAction(Request $request)
    {
        $files = $request->files->get('file');
        /* Make sure that Upload Directory is properly configured and writable */
        $path = ROOT_PATH . '/../web/upload/';
        $filename = $files['FileUpload']->getClientOriginalName();
        $files['FileUpload']->move($path,$filename);
        $message = 'File was successfully uploaded!';
    }
}
