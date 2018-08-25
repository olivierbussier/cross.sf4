<?php

class ConvertDatabase
{
    /**
     * @var array
     * @uses set_roles,json,create_password,nullable,integer,boolean,myDate,create_username, getImage
     */
    private $correspAdherents = [
        "id"                    => ["Ref"           , "integer"],
        "username"              => [""              , "create_username"],
        "nom"                   => ["NOM"           , ""],
        "prenom"                => ["PRENOM"        , ""],
        "mail"                  => ["MAIL"          , ""],
        "roles"                 => [""              , "set_roles"],
        "password"              => ["PASSWD"        , "create_password"],
    ];

    private $correspBlogTextes = [
        "id"                    => ["Ref"           , "integer"],
        "posted_at"             => ["Date"          , "myDate"],
        "title"                 => ["Title"         , ""],
        "content"               => ["Texte"         , ""],
        "position"              => ["Ordre"         , "integer"],
        "link"                  => ["Link"          , ""],
        "position_image"        => ["PositionImage" , ""],
        "image"                 => ["RefImage"      , "getImage"]
    ];

    private $id = 0;
    private $sql = [];
    private $data = [];
    private $sqlLast = [];

    private $baseSrc;
    private $baseDst;

    public function __construct()
    {
        $this->baseSrc = new mysqli('localhost', 'root', '', 'cross_old');
        $this->baseDst = new mysqli('localhost', 'root', '', 'cross');
    }

    /**
     * @param $field
     * @param $value
     */
    private function putSQL($field, $value)
    {
        $this->sql[$field] = $value;
    }

    private function myQuery(mysqli $m, $query)
    {
        echo $query . "\n\n";
        if (!$m->query($query)) {
            echo "Erreur SQL : " . $m->error . "\n";
        }
    }

    private function nullable($field, $value)
    {
        if ($value == 0) {
            $value = 'null';
        }
        $this->putSQL($field, $value);
    }

    private function integer(string $field, string $val)
    {
        $this->putSQL($field, $val);
    }

    /**
     * @param string $field
     * @param string $val
     */
    private function boolean(string $field, string $val)
    {
        switch ($val) {
            case "OUI":
            case "OK":
                $v = "true";
                break;
            case "":
            case "NON":
            case "KO":
                $v = "false";
                break;
            default:
                echo "erreur inconnue xxx '$val' xxx\n";
                exit;
        }
        $this->putSQL($field, $v);
    }

    /**
     * @param string $field
     * @param string $val
     */
    private function myDate(string $field, $val)
    {
        $d = explode("-", $val);
        if (@checkdate($d[1], $d[2], $d[0]))
            $date = "'" . $val . "'";
        else
            $date = 'null';
        $this->putSQL($field, $date);
    }

    /**
     * @param string $field
     * @param string $val
     */
    private function create_password(string $field, string $val)
    {
        $this->putSQL($field, "'" . password_hash($val, PASSWORD_BCRYPT) . "'");
    }

    /**
     * @param string $field
     * @param string $val
     */
    private function getImage(string $field, string $val)
    {

        if ($val != 0) {
            $res = $this->baseSrc->query("select * from prod_blog_images where Ref = $val");
            $d = $res->fetch_assoc();
            $txt = "'" . $d['ImageSrc'] . "'";
        } else {
            $txt = 'null';
        }
        $this->putSQL($field, $txt);
    }

    private function set_roles(string $field, string $val)
    {

        $dest_roles = [];
        $dest_roles[] = 'ROLE_USER';
        $dest_roles[] = 'ROLE_ADMIN';

        $texte = serialize($dest_roles);
        $this->putSQL($field, "'" . $texte . "'");
    }

    /**
     * @param string $field
     * @param string $val
     */
    private function create_username(string $field, string $val)
    {
        $prenom = strtolower($this->data['PRENOM']);
        $prenom = str_replace(' ', '_', $prenom);
        $prenom = str_replace('-', '_', $prenom);

        $nom = strtolower($this->data['NOM']);
        $nom = str_replace(' ', '_', $nom);
        $nom = str_replace('-', '_', $nom);

        $this->putSQL($field, "'" . $prenom . '_' . $nom . "'");
    }

    private function transfertDatabase(string $tableSrc, string $tableDst, array $tabCorresp)
    {
        $this->sql = [];

        $res = $this->baseSrc->query("select * from $tableSrc");

        while ($this->data = $res->fetch_assoc()) {

            $this->id = $this->data['Ref'];
            echo $this->id . "\n";

            foreach ($tabCorresp as $key => $val) {
                $dest_field = $key;
                $source_field = $tabCorresp[$key][0];
                $action = $tabCorresp[$key][1];
                if ($action != "") {
                    // Appel de la fonction
                    if ($source_field != "")
                        $field = $this->data[$source_field];
                    else
                        $field = '';
                    $this->$action($dest_field, $field);
                } else {
                    $this->putSQL($dest_field, "'" . $this->baseDst->escape_string($this->data[$source_field]) . "'");
                }
            }
            $assigns = "";
            $sep = ' ';
            foreach ($this->sql as $k => $v) {
                $assigns .= "\n    $sep$k=$v";
                $sep = ', ';
            }
            $sqlfinal = "insert into $tableDst set$assigns";

            $this->myQuery($this->baseDst, $sqlfinal);

            if (count($this->sqlLast) > 0) {
                foreach ($this->sqlLast as $sq) {
                    $this->myQuery($this->baseDst, $sq);
                }
                //echo serialize($this->sqlLast) . "\n";
                $this->sqlLast = [];
            }
        }
    }

    private function emptyBase()
    {
        $this->myQuery($this->baseDst,'delete from users');
        $this->myQuery($this->baseDst,'delete from blog');
    }

    public function doConvert()
    {
        $this->emptyBase();

        $this->transfertDatabase('prod_liste', 'users', $this->correspAdherents);
        $this->transfertDatabase('prod_blog_text', 'blog', $this->correspBlogTextes);
    }
}
?><!DOCTYPE html><html>
    <head>
        <meta http-equiv="content-language" content="fr">
        <meta name="language" content="fr">
        <meta charset="UTF-8">
    </head>
    <body>
        <pre>
<?php

$context = new ConvertDatabase();
$context->doConvert();

?>
        </pre>
    </body>
</html>