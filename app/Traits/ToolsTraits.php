<?php


namespace App\Traits;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ToolsTraits
{

    public function saveImage($image, $saveFolder, $width = 900, $height = 600): ?string
    {
        try {

            // Check if image is valid base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                // Take out the base64 encoded text without mime type
                $image = substr($image, strpos($image, ',') + 1);
                // Get file extension
                $type = strtolower($type[1]); // jpg, png, gif

                // Check if file is an image
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('invalid image type');
                }
                $image = str_replace(' ', '+', $image);
                $image = base64_decode($image);

                if ($image === false) {
                    throw new \Exception('base64_decode failed');
                }
            } else {
                throw new \Exception('did not match data URI with image data');
            }


            $dir = $saveFolder;
            $file = Str::random() . '.' . $type;
            $absolutePath = public_path($dir);
            $relativePath = $dir . $file;
            if (!File::exists($absolutePath)) {
                File::makeDirectory($absolutePath, 0755, true);
            }

            //file_put_contents($relativePath, $image);
            $img = Image::make($image)->resize($width, $height);
            $img->save($relativePath);

            return $file;


        } catch (\Exception $exception) {
            //return $exception->getMessage();
            return null;
        }
    }

    public function deletePhotoOldPhoto($photoName, $saveFolder): void
    {
        try {
            if ($photoName) {
                $absolutePath = public_path($saveFolder . $photoName);
                File::delete($absolutePath);
            }
        } catch (\Exception $exception) {
            //            return '';
        }
    }

    public function saveFile($file, $saveFolder): ?string
    {
        try {
            if($file){
                //$filename = time() . $file->getClientOriginalName();

                $name = $file->hashName(); // Generate a unique, random name...
                //$extension = $file->getClientOriginalExtension();

                $path = public_path($saveFolder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                $file->move($path, $name);
                return $name;
            }

            return null;

        } catch (\Exception $exception) {
            //return $exception->getMessage();
            return null;
        }
    }





}
