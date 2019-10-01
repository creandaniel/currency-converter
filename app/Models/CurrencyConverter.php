
<?php 
namespace App\Models;
class UserModel {
    public $id;
    public $name;
    public $email;
    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
    }
}
