<?php

namespace App\Http\Controllers\Api;

use App\Classes\Upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ManageImageController extends Controller
{
    public function getImages(Request $request, bool $isResponse = true)
    {
        // Đường dẫn đến thư mục chính
        $directory = 'uploads/photos/admin';

        // Kiểm tra xem thư mục có tồn tại không
        if (!Storage::exists($directory)) {
            return response()->json(['message' => 'Directory not found'], 404);
        }

        // Lấy tất cả các file trong các thư mục con 'thumb' và 'naib'
        $thumbFiles = Storage::allFiles($directory . '/thumb');
        $naibFiles = Storage::allFiles($directory . '/album');


        // Gộp tất cả các file lại với nhau
        $files = array_merge($thumbFiles, $naibFiles);

        // Lọc ra chỉ các file ảnh
        $images = array_filter($files, function ($file) {
            return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        });

        // Tạo mảng các thông tin ảnh với thời gian upload
        $imageData = array_map(function ($image) {
            return [
                'url' => Storage::url($image), // Tạo URL từ Storage
                'name' => pathinfo($image, PATHINFO_FILENAME), // Tên file
                'created_at' => Storage::lastModified($image), // Thời gian tạo file
            ];
        }, $images);

        // Sắp xếp ảnh theo thời gian đăng (mới nhất trước)
        usort($imageData, function ($a, $b) {
            return $b['created_at'] - $a['created_at']; // Sắp xếp giảm dần theo thời gian
        });

        // Phân trang
        $perPage = (int) $request->input('perPage', 10); // Số ảnh mỗi trang (mặc định 10)
        $page = (int) $request->input('page', 1); // Trang hiện tại (mặc định trang 1)
        $offset = ($page - 1) * $perPage;
        $paginatedImages = array_slice($imageData, $offset, $perPage);

        // Tính tổng số trang
        $total = count($imageData);
        $totalPages = ceil($total / $perPage);

        return $isResponse ? response()->json([
            'success' => true,
            'data' => $paginatedImages,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $total,
                'perPage' => $perPage,
            ]
        ]) : ['data' => $paginatedImages];
    }



    public function postImages(Request $request)
    {
        try {
            $payload = request()->except('_token');
            $files = $payload['files'] ?? null;
            $folderName = 'others';

            if (isset($payload['folder_name']) && ! empty($payload['folder_name'])) {
                $folderName = $payload['folder_name'];
            }

            // Xu ly anh resize
            if (isset($files) && ! empty($files)) {
                foreach ($files as $key => $file) {
                    $message = Upload::uploadImage($file, $folderName);
                    if ($message['status'] == 'error') {
                        $messages[] = $message['message'];
                    }
                }
            }

            $data = $this->getImages($request, false);

            return response()->json([
                'success'   => true,
                'message' => 'Tải ảnh thành công.',
                'data' => $data['data'],
            ]);
        } catch (Exception $e) {
            logInfo($e->getMessage());
        }
    }

    public function destroyImages(string $id)
    {
        try {
            $url = request('url');
            $filePath = str_replace(env('APP_URL') . '/storage/', '', $url);

            if (Storage::exists($filePath)) {

                Storage::delete($filePath);
            }

            $data = $this->getImages(request(), false);

            return response()->json(['success' => true, 'data' => $data['data'], 'message' => 'Images deleted successfully']);
        } catch (Exception $e) {
            logInfo($e->getMessage());
            // return errorResponse(__('messages.upload.delete.error'));
        }
    }
}
