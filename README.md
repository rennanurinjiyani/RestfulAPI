# Rest-API-with-CodeIgniter4
repository ini berisi contoh program rest-api pada CodeIgniter4

Berikut merupakan langkah-langkah membuat rest-api pada CodeIgniter4

# Tools yang dibutuhkan :
1. xampp
2. CodeIgniter4
3. Text Editor
4. Postman
5. Database editor

# Membuat Database Baru
untuk membuat database baru, aktifkan xampp terlebih dahulu pada module Apache dan MySQL. Setelah itu buka Database editor yang ingin digunakan(contohnya localhost/phpmyadmin). Lalu buat database dengan nama restful_db.

# Membuat Tabel Baru
Untuk membuat tabel baru. Setelah membuat database, buat tabel dengan nama Products.Untuk membuat table product, dapat dilakukan dengan mengeksekusi perintah SQL berikut:
CREATE TABLE product(
product_id INT(11) PRIMARY KEY AUTO_INCREMENT,
product_name VARCHAR(200),
product_price DOUBLE
)ENGINE=INNODB;

selanjutnya masukan nilai kedalam tabel dengan mengeksekusi perintah SQL berikut :
INSERT INTO product(product_name,product_price) VALUES
('Product 1','2000'),
('Product 2','5000'),
('Product 3','4000'),
('Product 4','6000'),
('Product 5','7000');

# Menginstall CodeIgniter4
Download file CodeIgniter4 pada link https://codeigniter.com lalu ekstrak di web server milik kita. Karena menggunakan xampp maka file diekstrak ke folder C:\xampp\htdocs. Kemudian ubah nama folder menjadi restfulapi

# Membuat Koneksi Database
Buka zle “Database.php” yang terdapat pada folder “app/Conzg”, kemudian temukan kode berikut:
 'DSN' => '',
 'hostname' => 'localhost',
 'username' => '',
 'password' => '',
 'database' => '',
 'DBDriver' => 'MySQLi',
 'DBPrefix' => '',
 'pConnect' => false,
 'DBDebug' => (ENVIRONMENT !== 'production'),
 'cacheOn' => false,
 'cacheDir' => '',
 'charset' => 'utf8',
 'DBCollat' => 'utf8_general_ci',
 'swapPre' => '',
 'encrypt' => false,
 'compress' => false,
 'strictOn' => false,
 'failover' => [],
 'port' => 3306,
];

Kemudian ubah menjadi seperti berikut:
public $default = [
 'DSN' => '',
 'hostname' => 'localhost',
 'username' => 'root',
 'password' => '',
 'database' => 'restful_db',
 'DBDriver' => 'MySQLi',
 'DBPrefix' => '',
 'pConnect' => false,
 'DBDebug' => (ENVIRONMENT !== 'production'),
 'cacheOn' => false,
 'cacheDir' => '',
 'charset' => 'utf8',
 'DBCollat' => 'utf8_general_ci',
 'swapPre' => '',
 'encrypt' => false,
 'compress' => false,
 'strictOn' => false,
 'failover' => [],
 'port' => 3306,
];

Temukan file env pada root project lalu ubah menjadi .env
Kemudian code # CI_ENVIRONMENT = production diubah menjadi CI_ENVIRONMENT = development

# Membuat File Model
Buat sebuah zle model bernama “ProductModel.php” pada folder “app/Models”, kemudian ketikan kode berikut:
<?php namespace App\Models;
use CodeIgniter\Model;
class ProductModel extends Model
{
 protected $table = 'product';
 protected $primaryKey = 'product_id';
 protected $allowedFields = ['product_name','product_price'];
}

# Membuat File Controller
Buat sebuah zle controller bernama “Products.php” pada folder “app/Controllers”, kemudian ketikan kode berikut:
<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;
class Products extends ResourceController
{
 use ResponseTrait;
 // get all product
 public function index()
 {
 $model = new ProductModel();
 $data = $model->findAll();
 return $this->respond($data, 200);
 }
 // get single product
 public function show($id = null)
 {
 $model = new ProductModel();
 $data = $model->getWhere(['product_id' => $id])->getResult();
 if($data){
 return $this->respond($data);
 }else{
 return $this->failNotFound('No Data Found with id '.$id);
 }
 }
 // create a product
 public function create()
 {
 $model = new ProductModel();
 $data = [
 'product_name' => $this->request->getPost('product_name'),
 'product_price' => $this->request->getPost('product_price')
 ];
 $data = json_decode(file_get_contents("php://input (php://input)"));
 //$data = $this->request->getPost();
$model->insert($data);
 $response = [
 'status' => 201,
 'error' => null,
 'messages' => [
 'success' => 'Data Saved'
 ]
 ];
 
 return $this->respondCreated($data, 201);
 }
 // update product
 public function update($id = null)
 {
 $model = new ProductModel();
 $json = $this->request->getJSON();
 if($json){
 $data = [
 'product_name' => $json->product_name,
 'product_price' => $json->product_price
 ];
 }else{
 $input = $this->request->getRawInput();
 $data = [
 'product_name' => $input['product_name'],
 'product_price' => $input['product_price']
 ];
 }
 // Insert to Database
 $model->update($id, $data);
 $response = [
 'status' => 200,
 'error' => null,
 'messages' => [
 'success' => 'Data Updated'
 ]
 ];
 return $this->respond($response);
 }
 // delete product
 public function delete($id = null)
 {
 $model = new ProductModel();
 $data = $model->find($id);
 if($data){
 $model->delete($id);
 $response = [
 'status' => 200,
 'error' => null,
 'messages' => [
 'success' => 'Data Deleted'
 ]
 ];
 
 return $this->respondDeleted($response);
 }else{
 return $this->failNotFound('No Data Found with id '.$id);
 }
 
 } 
 }

# Konfigurasi Routes.php
Buka zle “Routes.php” pada folder “app/Config”, kemudian temukan kode berikut:
$routes->get('/', 'Home::index');

Kemudian, ganti menjadi berikut:
$routes->resource('products');

# Aktifkan CORS (Cross-Origin Resources Sharing)
Untuk menaktifkan CORS, buat zle bernama “Cors.php” pada folder “app/Filters”. Kemudian ketikan kode berikut:
<?php namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
Class Cors implements FilterInterface
{
 public function before(RequestInterface $request, $arguments = null)
 {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-Window");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "OPTIONS") {
    die();
    }
 }
 public function after(RequestInterface $request, ResponseInterface $respons, $arguments = NULL)
 {
 // Do something here
 } }

Selanjutnya buka zle “Filters.php” yang terdapat pada folder “app/Conzg”. Kemudian temukan kode berikut:
public $aliases = [
 'csrf' => \CodeIgniter\Filters\CSRF::class,
 'toolbar' => \CodeIgniter\Filters\DebugToolbar::class,
 'honeypot' => \CodeIgniter\Filters\Honeypot::class,
 ];

 Kemudian tambahkan cors zlter seperti berikut:
 public $aliases = [
 'csrf' => \CodeIgniter\Filters\CSRF::class,
 'toolbar' => \CodeIgniter\Filters\DebugToolbar::class,
 'honeypot' => \CodeIgniter\Filters\Honeypot::class,
 'cors' => \App\Filters\Cors::class, 
 ];

 Selanjutnya deznisikan "cors" pada public globals seperti berikut:
 public $globals = [
 'before' => [
 'cors'
 //'honeypot'
 // 'csrf',
 ],
 'after' => [
 'toolbar',
 //'honeypot'
 ],
 ];

 # Testing
Jalankan project dengan mengetikkan perintah 'php spark serve' pada Terminal / Command promt 
Kemudian jalankan aplikasi POSTMAN dan ketikan URL 'http://localhost:8080/products' pada kolom URL POSTMAN.
Pilih method GET, kemudian klik tombol Send.
Selanjutnya Ketikan URL 'http://localhost:8080/products/12' pada kolom URL untuk mendapatkan single product.
Pilih dengan method GET, kemudian klik tombol Send.
Selanjutnya Ketikan URL 'http://localhost:8080/products' pada kolom URL untuk meng-create new product
Pilih method POST => Body => form-urlencoded => Masukan KEY dan VALUE => klik Send.
Ketikan URL http://localhost:8080/products/12 pada kolom URL untuk meng-update product
Pilih method PUT => Body => form-urlencoded => Masukan KEY dan VALUE => klik Send.
Ketikan URL http://localhost:8080/products/13 pada kolom URL untuk meng-hapus product
Pilih method DELETE, kemudian klik Send