<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadAttachamentRequest;
use App\Model\Attachment;
use App\Providers\AttachmentServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Process the attachment and upload it to the selected storage driver.
     *
     * @param UploadAttachamentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadAttachamentRequest $request)
    {
        $file = $request->file('file');
        $type = $request->route('type');

        $fileMimeType = $file->getClientMimeType();

        try {
            switch ($fileMimeType) {
                case 'video/mp4':
                case 'video/avi':
                case 'video/quicktime':
                case 'video/x-m4v':
                case 'video/mpeg':
                case 'video/wmw':
                case 'video/x-matroska':
                case 'video/x-ms-asf':
                case 'video/x-ms-wmv':
                case 'video/x-ms-wmx':
                case 'video/x-ms-wvx':
                    $directory = 'videos';
                    break;
                case 'audio/mpeg':
                case 'audio/ogg':
                case 'audio/wav':
                    $directory = 'audio';
                    break;
                default:
                    $directory = 'images';
                    break;
            }

            if ($type == 'post') {
                $directory = 'posts/'.$directory;
            } elseif ($type == 'message') {
                $directory = 'messenger/'.$directory;
            }

            $attachment = AttachmentServiceProvider::createAttachment($file, $directory);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]], 500);
        }

        return response()->json([
            'success' => true,
            'attachmentID' => $attachment->id,
            'path' => Storage::url($attachment->filename),
            'type' => AttachmentServiceProvider::getAttachmentType($attachment->type),
            'thumbnail' => AttachmentServiceProvider::getThumbnailPathForAttachmentByResolution($attachment, 150, 150),
        ]);
    }

    /**
     * Removes attachment out of db & out of the storage driver.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAttachment(Request $request)
    {
        try {
            $attachment = Attachment::where('id', $request->get('attachmentId'))->first();

            if ($attachment != null) {
                AttachmentServiceProvider::removeAttachment($attachment);
                $attachment->delete();
            }

            return response()->json(['success' => false, 'data' => [__('Attachments removed successfully')]]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }
}
