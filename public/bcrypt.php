<?php

class ConvertDatabase
{
    /**
     * @var array
     * @uses set_roles,json,create_password,nullable,integer,boolean,myDate,create_username,getImage,nonnul
     * @uses convertTemps,categorie
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

    private $correspResultats = [
        "id"                    => ["Ref"           , "integer"],
        "annee_cross"           => ["annee_cross"   , "integer"],
        "course"                => ["course"        , "nonnul"],
        "classement"            => ["arrive"        , "integer"],
        "dossard"               => ["dossard"       , "integer"],
        "temps"                 => ["temps"         , "convertTemps"],
        "ecart"                 => ["ecart"         , "convertTemps"],
        //"vitesse"               => ["vitesse"       , ""],  // Calculé par convertTemps
        "nom"                   => ["nom"           , ""],
        "prenom"                => ["prenom"        , ""],
        "categorie"             => ["categorie"     , "categorie"],
        "sexe"                  => ["sexe"          , ""],
        "ville"                 => ["ville"         , ""]
    ];

    private $id = 0;
    private $sql = [];
    private $data = [];
    private $sqlLast = [];

    private $baseSrc;
    private $baseDst;

    private $translation = [
            'à' => 'a',            'á' => 'a',            'â' => 'a',            'ã' => 'a',            'ä' => 'a',
            'ç' => 'c',
            'è' => 'e',            'é' => 'e',            'ê' => 'e',            'ë' => 'e',
            'ì' => 'i',            'í' => 'i',            'î' => 'i',            'ï' => 'i',
            'ñ' => 'n',
            'ò' => 'o',            'ó' => 'o',            'ô' => 'o',            'õ' => 'o',            'ö' => 'o',
            'ù' => 'u',            'ú' => 'u',            'û' => 'u',            'ü' => 'u',
            'ý' => 'y',            'ÿ' => 'y',
            'À' => 'A',            'Á' => 'A',            'Â' => 'A',            'Ã' => 'A',            'Ä' => 'A',
            'Ç' => 'C',
            'È' => 'E',            'É' => 'E',            'Ê' => 'E',            'Ë' => 'E',
            'Ì' => 'I',            'Í' => 'I',            'Î' => 'I',            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',            'Ó' => 'O',            'Ô' => 'O',            'Õ' => 'O',            'Ö' => 'O',
            'Ù' => 'U',            'Ú' => 'U',            'Û' => 'U',            'Ü' => 'U',
            'Ý' => 'Y'
    ];

    private $tabCat = [
        's'     => 'Senior',
        'v1'    => 'Vétéran1',
        'v2'    => 'Vétéran 2',
        'v3'    => 'Vétéran 3',
        'c'     => 'Cadet',
        'e'     => 'Espoir',
        'j'     => 'Junior',
        '0'     => '-',
        'v4'    => 'Vétéran 4',
        'se'    => 'Senior',
        'es'    => 'Espoir',
        'ca'    => 'Cadet',
        'ju'    => 'Junior',
        'senio' => 'Senior',
        'veter' => 'Vétéran',
        'junio' => 'Junior',
        'espoi' => 'Espoir',
        'cadet' => 'Cadet',
        'v5'    => 'Vétéran 5'
];
    /**
     * ConvertDatabase constructor.
     */
    public function __construct()
    {
        $this->baseSrc = new mysqli('localhost', 'root', '', 'cross');
        $this->baseDst = new mysqli('localhost', 'root', '', 'cross');

        $this->baseSrc->set_charset('utf8');
        $this->baseDst->set_charset('utf8');
    }

    /**
     * @param $field
     * @param $value
     */
    private function putSQL($field, $value)
    {
        $this->sql[$field] = $value;
    }

    /**
     * @param mysqli $m
     * @param $query
     */
    private function myQuery(mysqli $m, $query)
    {
        echo $query . "\n\n";
        if (!$m->query($query)) {
            echo "Erreur SQL : " . $m->error . "\n";
        }
    }

    /**
     * @param $field
     * @param $value
     * @return bool
     */
    private function categorie($field, $value)
    {
        $cat = strtr($value,$this->translation);
        $cat = strtolower($cat);

        if (isset($this->tabCat[$cat])) {
            $this->putSQL($field, "'" . $this->tabCat[$cat] . "'");
            return true;
        } else {
            echo "Erreur : Correspondance catégorie non trouvée : " . $cat . "\n";
            $this->putSQL($field, "'" . $cat . "'");
            return false;
        }
    }

    /**
     * @param $field
     * @param $value
     * @return bool
     */
    private function nonnul($field, $value)
    {
        if (is_string($value)) {
            if ($value == '') {
                return false;
            } else {
                $this->putSQL($field, "'" . $value . "'");
            }
        } elseif (is_integer($value)) {
            if ($value == 0) {
                return false;
            } else {
                $this->putSQL($field, $value);
            }
        } else {
            $this->putSQL($field, "'" . $value . "'");
        }
        return true;
    }

    /**
     * @param $field
     * @param $value
     * @return bool
     */
    private function nullable($field, $value)
    {
        if ($value == 0) {
            $value = 'null';
        }
        $this->putSQL($field, $value);
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
     */
    private function integer(string $field, string $val)
    {
        $this->putSQL($field, $val);
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
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
        return true;
    }

    /**
     * @param $field
     * @param $val
     * @return bool
     */
    private function convertTemps($field, $val)
    {
        $val = trim($val);

        if ($val == "" || $val == '0' || $val == '0/0/0')
            $val="00:00:00";

        if (!($res = DateTime::createFromFormat('G i s', $val)) &&
            !($res = DateTime::createFromFormat('H i s', $val)) &&
            !($res = DateTime::createFromFormat('H\/i\/s', $val)) &&
            !($res = DateTime::createFromFormat('G\/i\/s', $val)) &&
            !($res = DateTime::createFromFormat('G:i:s', $val)) &&
            !($res = DateTime::createFromFormat('H:i:s', $val)) &&
            !($res = DateTime::createFromFormat('H\ \h\ i\ \m\n\ s\ \s', $val)) &&
            !($res = DateTime::createFromFormat('G\ \h\ i\ \m\n\ s\ \s', $val))) {
            var_dump(DateTime::getLastErrors());
            echo "Pas de format pour : $val\n";
            return true;
        } else {
            $fmt = $res->format("H:i:s");
            //echo "$field : $val -> $fmt\n";
            $this->putSQL($field, "'" . $fmt . "'");

            if ($field == 'temps') {
                $t = explode(":",$fmt);
                $dt = ($t[0] + $t[1]/60 + $t[2]/3600);
                $vitesse = 10.0 / $dt;
                //echo "vitesse = $vitesse\n";
                $this->putSQL("vitesse", sprintf("'%02.2f Km/h'",$vitesse));
            }
        }
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
     */
    private function myDate(string $field, $val)
    {
        $d = explode("-", $val);
        if (@checkdate($d[1], $d[2], $d[0]))
            $date = "'" . $val . "'";
        else
            $date = 'null';
        $this->putSQL($field, $date);
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
     */
    private function create_password(string $field, string $val)
    {
        $this->putSQL($field, "'" . password_hash($val, PASSWORD_BCRYPT) . "'");
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
     */
    private function getImage(string $field, string $val)
    {

        if ($val != 0) {
            $res = $this->baseSrc->query("select * from preprod_blog_images where Ref = $val");
            $d = $res->fetch_assoc();
            $txt = "'" . $d['ImageSrc'] . "'";
        } else {
            $txt = 'null';
        }
        $this->putSQL($field, $txt);
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
     */
    private function set_roles(string $field, string $val)
    {

        $dest_roles = [];
        $dest_roles[] = 'ROLE_USER';
        $dest_roles[] = 'ROLE_ADMIN';

        $texte = serialize($dest_roles);
        $this->putSQL($field, "'" . $texte . "'");
        return true;
    }

    /**
     * @param string $field
     * @param string $val
     * @return bool
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
        return true;
    }

    /**
     * @param string $tableSrc
     * @param string $tableDst
     * @param array $tabCorresp
     */
    private function transfertDatabase(string $tableSrc, string $tableDst, array $tabCorresp)
    {

        $res = $this->baseSrc->query("select * from $tableSrc");

        while ($this->data = $res->fetch_assoc()) {

            $this->id = $this->data['Ref'];
            //echo $this->id . "\n";
            $this->sql = [];
            $row = true;

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
                    if (!$this->$action($dest_field, $field) == true) {
                        $row = false;
                        break;
                    }
                } else {
                    $string = "'" . $this->baseDst->escape_string(trim($this->data[$source_field])) . "'";
                    $this->putSQL($dest_field, $string );
                }
            }
            if (!$row) {
                continue;
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

    /**
     *
     */
    private function emptyBase()
    {
        $this->myQuery($this->baseDst,'delete from users');
        $this->myQuery($this->baseDst,'delete from blog');
        $this->myQuery($this->baseDst,'delete from resultat');
    }

    /**
     *
     */
    public function doConvert()
    {
        $this->emptyBase();

        $this->transfertDatabase('preprod_liste', 'users', $this->correspAdherents);
        $this->transfertDatabase('preprod_blog_text', 'blog', $this->correspBlogTextes);
        $this->transfertDatabase('preprod_classement', 'resultat', $this->correspResultats);
    }
}
?><!DOCTYPE html><html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta http-equiv="content-language" content="fr">
        <meta name="language" content="fr">
        <meta charset="utf-8">
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