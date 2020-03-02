# PHP SDK for ImageKit

[![PHP CI](https://github.com/imagekit-developer/imagekit-php/workflows/PHP%20CI/badge.svg)](https://github.com/imagekit-developer/imagekit-php/) [![Packagist](https://img.shields.io/packagist/v/imagekit/imagekit.svg)](https://packagist.org/packages/imagekit/imagekit) [![Packagist](https://img.shields.io/packagist/dt/imagekit/imagekit.svg)](https://packagist.org/packages/imagekit/imagekit) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Twitter Follow](https://img.shields.io/twitter/follow/imagekitio?label=Follow&style=social)](https://twitter.com/ImagekitIo)

PHP SDK for [ImageKit](https://imagekit.io/) that implements the new APIs and interface for performing different file
operations.

ImageKit is a complete image optimization and transformation solution that comes with and
[image CDN](https://imagekit.io/features/imagekit-infrastructure) and media storage. It can be integrated with your
existing infrastructure - storages like AWS s3, web servers, your CDN and custom domain names, allowing you to deliver
optimize images in minutes with minimal code changes.

Table of contents -

-   [Installation](#Installation)
-   [Initialization](#Initialization)
-   [URL Generation](#URL-generation)
-   [File Upload](#File-upload)
-   [File Management](#File-management)
-   [Utility Functions](#Utility-functions)
-   [Support](#Support)
-   [Links](#Links)

## Installation

Go to your terminal and type the following command

```sh
composer require imagekit/imagekit
```

## Initialization

```
use ImageKit\ImageKit;

$imageKit = new ImageKit(
    "your_public_key",
    "your_private_key",
    "your_url_endpoint"
);
```

## Usage

You can use this PHP SDK for 3 different kinds of methods - URL generation, file upload and file management.
The usage of the SDK has been explained below

## URL generation

**1. Using Image path and image hostname or endpoint**

This method allows you to create a URL using the path where the image exists and the URL
endpoint(url_endpoint) you want to use to access the image. You can refer to the documentation
[here](https://docs.imagekit.io/integration/url-endpoints) to read more about URL endpoints
in ImageKit and the section about [image origins](https://docs.imagekit.io/integration/configure-origin) to understand about paths with different kinds of origins.

```
$imageURL = $imageKit->url(array(
    "path" => "/default-image.jpg",
    "transformation" => array(
        array(
            "height" => "300",
            "width" => "400",
        )
    )
));
```

The result in a URL like

```
https://ik.imagekit.io/your_imagekit_id/endpoint/tr:h-300,w-400/default-image.jpg
```

**2.Using full image URL**
This method allows you to add transformation parameters to and existing, complete URL that is already mapped to ImageKit
using `src` parameter. This method should be used if you have the complete image URL mapped to ImageKit stored in your
database.

```
$imageURL = $imageKit->url(array(
    "src" => "https://ik.imagekit.io/your_imagekit_id/endpoint/default-image.jpg",
    "transformation" => array(
        array(
            "height" => "300",
            "width" => "400",
        )
    )
));

```

This results in a URL like

```
https://ik.imagekit.io/your_imagekit_id/endpoint/default-image.jpg?tr=h-300%2Cw-400
```

The `$imageKit->url()` method accepts the following parameters

| Option                | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| :-------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| urlEndpoint           | Optional. The base URL to be appended before the path of the image. If not specified, the URL Endpoint specified at the time of SDK initialization is used. For example, https://ik.imagekit.io/your_imagekit_id/endpoint/                                                                                                                                                                                                                                                                                                                                                               |
| path                  | Conditional. This is the path at which the image exists. For example, `/path/to/image.jpg`. Either the `path` or `src` parameter need to be specified for URL generation.                                                                                                                                                                                                                                                                                                                                                                                                                |
| src                   | Conditional. This is the complete URL of an image already mapped to ImageKit. For example, `https://ik.imagekit.io/your_imagekit_id/endpoint/path/to/image.jpg`. Either the `path` or `src` parameter need to be specified for URL generation.                                                                                                                                                                                                                                                                                                                                           |
| transformation        | Optional. An array of objects specifying the transformation to be applied in the URL. The transformation name and the value should be specified as a key-value pair in the object. Different steps of a [chained transformation](https://docs.imagekit.io/features/image-transformations/chained-transformations) can be specified as different objects of the array. The complete list of supported transformations in the SDK and some examples of using them are given later. If you use a transformation name that is not specified in the SDK, it gets applied as it is in the URL. |
| transformationPostion | Optional. Default value is `path` that places the transformation string as a path parameter in the URL. Can also be specified as `query` which adds the transformation string as the query parameter `tr` in the URL. If you use `src` parameter to create the URL, then the transformation string is always added as a query parameter.                                                                                                                                                                                                                                                 |
| queryParameters       | Optional. These are the other query parameters that you want to add to the final URL. These can be any query parameters and not necessarily related to ImageKit. Especially useful, if you want to add some versioning parameter to your URLs.                                                                                                                                                                                                                                                                                                                                           |
| signed                | Optional. Boolean. Default is `false`. If set to `true`, the SDK generates a signed image URL adding the image signature to the image URL. This can only be used if you are creating the URL with the `urlEndpoint` and `path` parameters, and not with the `src` parameter.                                                                                                                                                                                                                                                                                                             |
| expireSeconds         | Optional. Integer. Meant to be used along with the `signed` parameter to specify the time in seconds from now when the URL should expire. If specified, the URL contains the expiry timestamp in the URL and the image signature is modified accordingly.                                                                                                                                                                                                                                                                                                                                |

#### Examples of generating URLs

**1. Chained Transformations as a query parameter**

```
$imageURL = $imageKit->url(array(
    "path" => "/default-image.jpg",
    "url_endpoint" => "https://ik.imagekit.io/your_imagekit_id/endpoint/",
    "transformation" => array(
        array(
            "height" => "300",
            "width" => "400",
        ),
        array(
            "rotation" => 90
        ),
    ),
    "transformationPosition" => "query"
));
```

```
https://ik.imagekit.io/your_imagekit_id/endpoint/default-image.jpg?tr=h-300%2Cw-400%3Art-90
```

**2. Sharpening and contrast transforms and a progressive JPG image**

There are some transforms like [Sharpening](https://docs.imagekit.io/features/image-transformations/image-enhancement-and-color-manipulation) that can be added to the URL with or without any other value. To use such transforms without specifying a value, specify the value as "-" in the transformation object, otherwise, specify the value that you want to be added to this transformation.

```
$imageURL = $imageKit->url(array(
    "src" => "https://ik.imagekit.io/your_imagekit_id/endpoint/default-image.jpg",
    "transformation" => array(
        array(
            "format" => "jpg",
            "progressive" => "true",
            "effectSharpen" => "-",
            "effectContrast" => "1"
        )
    )
));
```

```
//Note that because `src` parameter was used, the transformation string gets added as a query parameter `tr`
https://ik.imagekit.io/your_imagekit_id/endpoint/default-image.jpg?tr=f-jpg%2Cpr-true%2Ce-sharpen%2Ce-contrast-1
```

**3. Signed URL that expires in 300 seconds with the default URL endpoint and other query parameters**

```
 $imageURL = $imageKit->url(array(
    "path" => "/default-image.jpg",
    "queryParameters" => array(
        "v" => "123",
    ),
    "transformation" => array(
        array(
            "height" => "300",
            "width" => "400"
        ),
    ),
    "signed" => true,
    "expireSeconds" => 300,
));
```

```
https://ik.imagekit.io/your_imagekit_id/tr:h-300,w-400/default-image.jpg?v=123&ik-t=1567358667&ik-s=f2c7cdacbe7707b71a83d49cf1c6110e3d701054
```

#### List of supported transformations

The complete list of transformations supported and their usage in ImageKit can be found [here](https://docs.imagekit.io/features/image-transformations). The SDK gives a name to each transformation parameter, making the code simpler and readable. If a transformation is supported in ImageKit, but a name for it cannot be found in the table below, then use the transformation code from ImageKit docs as the name when using in the `url` function.

| Supported Transformation Name | Translates to parameter |
| ----------------------------- | ----------------------- |
| height                        | h                       |
| width                         | w                       |
| aspectRatio                   | ar                      |
| quality                       | q                       |
| crop                          | c                       |
| cropMode                      | cm                      |
| x                             | x                       |
| y                             | y                       |
| focus                         | fo                      |
| format                        | f                       |
| radius                        | r                       |
| background                    | bg                      |
| border                        | bo                      |
| rotation                      | rt                      |
| blur                          | bl                      |
| named                         | n                       |
| overlayImage                  | oi                      |
| overlayX                      | ox                      |
| overlayY                      | oy                      |
| overlayFocus                  | ofo                     |
| overlayHeight                 | oh                      |
| overlayWidth                  | ow                      |
| overlayText                   | ot                      |
| overlayTextFontSize           | ots                     |
| overlayTextFontFamily         | otf                     |
| overlayTextColor              | otc                     |
| overlayAlpha                  | oa                      |
| overlayTextTypography         | ott                     |
| overlayBackground             | obg                     |
| overlayImageTrim              | oit                     |
| progressive                   | pr                      |
| lossless                      | lo                      |
| trim                          | t                       |
| metadata                      | md                      |
| colorProfile                  | cp                      |
| defaultImage                  | di                      |
| dpr                           | dpr                     |
| effectSharpen                 | e-sharpen               |
| effectUSM                     | e-usm                   |
| effectContrast                | e-contrast              |
| effectGray                    | e-grayscale             |
| original                      | orig                    |

## File Upload

The SDK provides a simple interface using the `$imageKit->upload()` method to upload files to the ImageKit Media Library. It accepts all the parameters supported by the [ImageKit Upload API](https://docs.imagekit.io/api-reference/upload-file-api).

The `upload()` method requires at least the `file` and the `fileName` parameter to upload a file and returns a callback with the `error` and `result` as arguments. You can pass other parameters supported by the ImageKit upload API using the same parameter name as specified in the upload API documentation. For example, to specify tags for a file at the time of upload use the `tags` parameter as specified in the [documentation here](https://docs.imagekit.io/api-reference/upload-file-api/server-side-file-upload).

Sample usage

```
$imageKit->uploadFiles(array(
    'file' => "your_file",
    'fileName' => "your_file_name.jpg",
));
```

## Available options for request

<table class="table-0f56c2d8" data-key="12024">

<tbody>

<tr class="tableRow-41a0302b" data-key="11680">

<td data-table="cell" class="tableCell-150ac604" data-key="11676" style="text-align: left;">

<span class="text-4505230f--UIH400-4e41e82a--textContentFamily-49a318e1"><span data-key="11674"><span data-offset-key="11674:0">Parameter</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11679" style="text-align: left;">

<span class="text-4505230f--UIH400-4e41e82a--textContentFamily-49a318e1"><span data-key="11677"><span data-offset-key="11677:0">Description</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11724">

<td data-table="cell" class="tableCell-150ac604" data-key="11697" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11681"><span data-offset-key="11681:0">**file**</span> <span data-offset-key="11681:1">required</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11723" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11698"><span data-offset-key="11698:0">This field accepts three kinds of values: -</span> <span data-offset-key="11698:1">`binary`</span> <span data-offset-key="11698:2">- You can send the content of the file as binary. This is used when a file is being uploaded from the browser. -</span> <span data-offset-key="11698:3">`base64`</span> <span data-offset-key="11698:4">- Base64 encoded string of file content. -</span> <span data-offset-key="11698:5">`url`</span> <span data-offset-key="11698:6">- URL of the file from where to download the content before uploading. Downloading file from URL might take longer, so it is recommended that you pass the binary or base64 content of the file. Pass the full URL, for example -</span> <span data-offset-key="11698:7">`https://www.example.com/rest-of-the-image-path.jpg`</span><span data-offset-key="11698:8">.</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11742">

<td data-table="cell" class="tableCell-150ac604" data-key="11738" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11725"><span data-offset-key="11725:0">**fileName**</span> <span data-offset-key="11725:1">required</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11741" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11739"><span data-offset-key="11739:0">The name with which the file has to be uploaded.</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11792">

<td data-table="cell" class="tableCell-150ac604" data-key="11756" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11743"><span data-offset-key="11743:0">**useUniqueFileName**</span> <span data-offset-key="11743:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11791" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11757"><span data-offset-key="11757:0">Whether to use a unique filename for this file or not. - Accepts</span> <span data-offset-key="11757:1">`true`</span> <span data-offset-key="11757:2">or</span> <span data-offset-key="11757:3">`false`</span><span data-offset-key="11757:4">. - If set</span> <span data-offset-key="11757:5">`true`</span><span data-offset-key="11757:6">, ImageKit.io will add a unique suffix to the filename parameter to get a unique filename. - If set</span> <span data-offset-key="11757:7">`false`</span><span data-offset-key="11757:8">, then the image is uploaded with the provided filename parameter and any existing file with the same name is replaced.</span> <span data-offset-key="11757:9">**Default value**</span> <span data-offset-key="11757:10">-</span> <span data-offset-key="11757:11">`true`</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11837">

<td data-table="cell" class="tableCell-150ac604" data-key="11809" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11793"><span data-offset-key="11793:0">**tags**</span> <span data-offset-key="11793:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11836" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11810"><span data-offset-key="11810:0">Set the tags while uploading the file. - Comma-separated value of tags in format</span> <span data-offset-key="11810:1">`tag1,tag2,tag3`</span><span data-offset-key="11810:2">. For example -</span> <span data-offset-key="11810:3">`t-shirt,round-neck,men`</span> <span data-offset-key="11810:4">- The maximum length of all characters should not exceed 500. -</span> <span data-offset-key="11810:5">`%`</span> <span data-offset-key="11810:6">is not allowed. - If this field is not specified and the file is overwritten then the tags will be removed.</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11900">

<td data-table="cell" class="tableCell-150ac604" data-key="11854" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11838"><span data-offset-key="11838:0">**folder**</span> <span data-offset-key="11838:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11899" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11855"><span data-offset-key="11855:0">The folder path (e.g.</span> <span data-offset-key="11855:1">`/images/folder/`</span><span data-offset-key="11855:2">) in which the image has to be uploaded. If the folder(s) didn't exist before, a new folder(s) is created. The folder name can contain: - Alphanumeric Characters:</span> <span data-offset-key="11855:3">`a-z`</span> <span data-offset-key="11855:4">,</span> <span data-offset-key="11855:5">`A-Z`</span> <span data-offset-key="11855:6">,</span> <span data-offset-key="11855:7">`0-9`</span> <span data-offset-key="11855:8">- Special Characters:</span> <span data-offset-key="11855:9">` /``_ `</span> <span data-offset-key="11855:10">and</span> <span data-offset-key="11855:11">`-`</span> <span data-offset-key="11855:12">- Using multiple</span> <span data-offset-key="11855:13">`/`</span> <span data-offset-key="11855:14">creates a nested folder.</span> <span data-offset-key="11855:15">**Default value**</span> <span data-offset-key="11855:16">- /</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11947">

<td data-table="cell" class="tableCell-150ac604" data-key="11917" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="ed9720d221654292aa58142f879dea91"><span data-offset-key="ed9720d221654292aa58142f879dea91:0">**isPrivateFile**</span> <span data-offset-key="ed9720d221654292aa58142f879dea91:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11946" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11918"><span data-offset-key="11918:0">Whether to mark the file as private or not. This is only relevant for image type files. - Accepts</span> <span data-offset-key="11918:1">`true`</span> <span data-offset-key="11918:2">or</span> <span data-offset-key="11918:3">`false`</span><span data-offset-key="11918:4">. - If set</span> <span data-offset-key="11918:5">`true`</span><span data-offset-key="11918:6">, the file is marked as private which restricts access to the original image URL and unnamed image transformations without signed URLs. Without the signed URL, only named transformations work on private images</span> <span data-offset-key="11918:7">**Default value**</span> <span data-offset-key="11918:8">-</span> <span data-offset-key="11918:9">`false`</span></span></span>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="11988">

<td data-table="cell" class="tableCell-150ac604" data-key="11964" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="a7aff28dce734653b30bdcc49230ad13"><span data-offset-key="a7aff28dce734653b30bdcc49230ad13:0">**customCoordinates** </span><span data-offset-key="a7aff28dce734653b30bdcc49230ad13:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="11987" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="11965"><span data-offset-key="11965:0">Define an important area in the image. This is only relevant for image type files.</span></span></span>

-   <div data-key="c53fb2f1b56b418b9d6b9fb4a06f7792" class="reset-3c756112--listItemContent-756c9114">

    <span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="1362a1a179384b28ba7e977050546dab"><span data-offset-key="1362a1a179384b28ba7e977050546dab:0">To be passed as a string with the x and y coordinates of the top-left corner, and width and height of the area of interest in format</span> <span data-offset-key="1362a1a179384b28ba7e977050546dab:1">`x,y,width,height`</span><span data-offset-key="1362a1a179384b28ba7e977050546dab:2">. For example -</span> <span data-offset-key="1362a1a179384b28ba7e977050546dab:3">`10,10,100,100`</span></span></span>

    </div>

-   <div data-key="844aad56bbb048179413ba0ca4fa120e" class="reset-3c756112--listItemContent-756c9114">

    <span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="2a3ff3400c6142118f3d9c3bd8bce8b5"><span data-offset-key="2a3ff3400c6142118f3d9c3bd8bce8b5:0">Can be used with</span> <span data-offset-key="2a3ff3400c6142118f3d9c3bd8bce8b5:1">`fo-custom`</span><span data-offset-key="2a3ff3400c6142118f3d9c3bd8bce8b5:2">transformation.</span></span></span>

    </div>

-   <div data-key="6bd6a4128df149ccbf8ed85c23cb296a" class="reset-3c756112--listItemContent-756c9114">

    <span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="f6e308adfee44156ad8ed6b849a7e65e"><span data-offset-key="f6e308adfee44156ad8ed6b849a7e65e:0">If this field is not specified and the file is overwritten, then customCoordinates will be removed.</span></span></span>

    </div>

</td>

</tr>

<tr class="tableRow-41a0302b" data-key="12023">

<td data-table="cell" class="tableCell-150ac604" data-key="12002" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="455adcd180e141d2b1cc799f29b5b846"><span data-offset-key="455adcd180e141d2b1cc799f29b5b846:0">**responseFields**</span> <span data-offset-key="455adcd180e141d2b1cc799f29b5b846:1">optional</span></span></span>

</td>

<td data-table="cell" class="tableCell-150ac604" data-key="12022" style="text-align: left;">

<span class="text-4505230f--TextH400-3033861f--textContentFamily-49a318e1"><span data-key="12003"><span data-offset-key="12003:0">Comma-separated values of the fields that you want ImageKit.io to return in response. For example, set the value of this field to</span> <span data-offset-key="12003:1">`tags,customCoordinates,isPrivateFile,metadata`</span> <span data-offset-key="12003:2">to get value of</span> <span data-offset-key="12003:3">`tags`</span><span data-offset-key="12003:4">,</span> <span data-offset-key="12003:5">`customCoordinates`</span><span data-offset-key="12003:6">,</span> <span data-offset-key="12003:7">`isPrivateFile`</span> <span data-offset-key="12003:8">, and</span> <span data-offset-key="12003:9">`metadata`</span> <span data-offset-key="12003:10">in the response.</span></span></span>

</td>

</tr>

</tbody>

</table>

If the upload succeed, `error` will be `null` and the `result` will be the same as what is received from ImageKit's servers.
If the upload fails, `error` will be the same as what is received from ImageKit's servers and the `result` will be null.

## File Management

The SDK provides a simple interface for all the [media APIs mentioned here](https://docs.imagekit.io/api-reference/media-api) to manage your files. You can use a callback function with all API interfaces. The first argument of the callback function is the error and the second is the result of the API call. Error will be `null` if the API succeeds.

**1. List & Search Files**

Accepts an object specifying the parameters to be used to list and search files. All parameters specified in the [documentation here](https://docs.imagekit.io/api-reference/media-api/list-and-search-files) can be passed as is with the correct values to get the results.

```
$imageKit->listFiles(
    array(
        "skip" => 10,
        "limit" => 10
    )
);
```

**2. Get File Details**

Accepts the file ID and fetches the details as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/get-file-details).

```
$imageKit->getFileDetails("file_id");
```

**3. Get File Metadata**

Accepts the file ID and fetches the metadata as per the [API documentation here](https://docs.imagekit.io/api-reference/metadata-api/get-image-metadata-for-uploaded-media-files).

```
 $imageKit->getFileMetaData("file_id");
```

**4. Get File Metadata from remote URL**

Accepts the file ID and fetches image exif, pHash and other metadata from ImageKit.io powered remote URL as per the [API documentation here](https://docs.imagekit.io/api-reference/metadata-api/get-image-metadata-from-remote-url).

```
$imageKit->getFileMetadataFromRemoteURL("imagekit_remote_url")
```

**5. Update File Details**

Update parameters associated with the file as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/update-file-details). The first argument to the `updateFileDetails` method is the file ID and the second argument is an object with the parameters to be updated.

```
$imageKit->updateFileDetails("file_id", array("tags" => ['image_tag']));
```

**6. Delete File**

Delete a file as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/delete-file). The method accepts the file ID of the file that has to be deleted.

```
$imageKit->deleteFile("file_id");
```

**7. Delete Bulk Files by ID**

Deletes multiple files and all their transformations as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/delete-files-bulk). The method accepts the array of file IDs that has to be deleted.

```
$imageKit->bulkFileDeleteByIds(array(
    "fileIds" => ["file_id_1", "file_id_2", ...]
));
```

**8. Purge Cache**

Programmatically issue a cache clear request as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/purge-cache). Accepts the full URL of the file for which the cache has to be cleared.

```
$imageKit->purgeFileCacheApi(array("url" => "full_url"));
```

**9. Purge Cache Status**

Get the purge cache request status using the request ID returned when a purge cache request gets submitted as per the [API documentation here](https://docs.imagekit.io/api-reference/media-api/purge-cache-status)

```
$imageKit->purgeFileCacheApiStatus("cache_request_id");
```

## Utility functions

We have included following commonly used utility functions in this package.

### Authentication parameter generation

In case you are looking to implement client-side file upload, you are going to need a token, expiry timestamp and a valid signature for that upload. The SDK provides a simple method that you can use in your code to generate these authentication parameters for you.

_Note: The Private API Key should never be exposed in any client-side code. You must always generate these authentication parameters on the server-side_

```
$imageKit->getAuthenticationParameters($token = "", $expire = 0);
```

Returns

```
array(
    "token" => "unique_token",
    "expire" => "valid_expiry_timestamp",
    "signature" => "generated_signature",
);
```

Both the `token` and `expire` parameters are optional. If not specified the SDK uses the [uuid](https://www.npmjs.com/package/uuid) package to generate a random token and also generates a valid expiry timestamp internally. The value of the `token` and `expire` used to generate the signature are always returned in the response, no matter if they are provided as an input to this method or not.

### Distance calculation between two pHash values

Perceptual hashing allows you to constructing a hash value that uniquely identifies an input image based on the contents of an image. [ImageKit.io metadata API](https://docs.imagekit.io/api-reference/metadata-api) returns the pHash value of an image in the response. You can use this value to find a duplicate (or similar) image by calculating distance between pHash value of two images.

This SDK exposes `pHashDistance` function to calcualte distance between two pHash values. It accepts two pHash hexadecimal strings and returns a numeric value indicative of the level of difference between the two images.

```
  $imageKit->pHashDistance($firstHash ,$secondHash);
```

#### Distance calculation examples

```
$imageKit->pHashDistance('f06830ca9f1e3e90', 'f06830ca9f1e3e90');
// output: 0 (same image)

$imageKit->pHashDistance('2d5ad3936d2e015b', '2d6ed293db36a4fb');
// output: 17 (similar images)

$imageKit->pHashDistance('a4a65595ac94518b', '7838873e791f8400');
// output: 37 (dissimilar images)
```

## Sample Code Instruction

To run sample code go to sample directory and run

```
php sample.php
```

## Support

For any feedback or to report any issues or general implementation support please reach out to [support@imagekit.io](mailto:support@imagekit.io)

## Links

-   [Documentation](https://docs.imagekit.io)
-   [Main website](https://imagekit.io)

## License

Released under the MIT license.
