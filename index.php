<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

function getPartsFromFullname($fullname) {

    $explode = explode(" ", $fullname);

    return ["surname" => $explode[0], "name" => $explode[1], "patronymic" => $explode[2]];

}

function getFullnameFromParts($surname, $name, $patronymic) {

    return $surname . ' ' . $name . ' ' . $patronymic;

}

function getShortName($fullname) {

    $parts = getPartsFromFullname($fullname);

    return $parts['name'] . ' ' . mb_substr($parts['surname'], 0, 1) . '.';

}

function getGenderFromName($fullname) {

    $gender = 0;

    $parts = getPartsFromFullname($fullname);

    //Фамилия заканчивается на "вна", либо имя заканчивается на "а", либо фамилия заканчивается на "ва" - женский пол
    if (mb_substr($parts['patronymic'], mb_strlen($parts['patronymic'])-3, mb_strlen($parts['patronymic']))=="вна"
    or mb_substr($parts['name'], mb_strlen($parts['name'])-1, mb_strlen($parts['name']))=="а"
    or mb_substr($parts['surname'], mb_strlen($parts['surname'])-2, mb_strlen($parts['surname']))=="ва") $gender--;

    //Фамилия заканчивается на "ич", либо имя заканчивается на "й"/"н", либо фамилия заканчивается на "в" - женский пол
    if (mb_substr($parts['patronymic'], mb_strlen($parts['patronymic'])-2, mb_strlen($parts['patronymic']))=="ич"
    or mb_substr($parts['name'], mb_strlen($parts['name'])-1, mb_strlen($parts['name']))=="й"
    or mb_substr($parts['name'], mb_strlen($parts['name'])-1, mb_strlen($parts['name']))=="н"
    or mb_substr($parts['surname'], mb_strlen($parts['surname'])-2, mb_strlen($parts['surname']))=="в") $gender++;

    return $gender;

}

function getGenderDescription($users) {

    $men = array_filter($users, function($user) {
        return getGenderFromName($user['fullname'])===1;
    });

    $women = array_filter($users, function($user) {
        return getGenderFromName($user['fullname'])===-1;
    });

    $nodata = array_filter($users, function($user) {
        return getGenderFromName($user['fullname'])===0;
    });

    $result = "Гендерный состав аудитории:<br>";
    $result .= "---------------------------<br>";
    $result .= "Мужчины - " . round(count($men)/(count($users)/100)) . "%<br>";
    $result .= "Женщины - " . round(count($women)/(count($users)/100)) . "%<br>";
    $result .= "Не удалось определить - " . round(count($nodata)/(count($users)/100)) . "%<br>";

    return $result;

}

function getPerfectPartner($surname, $name, $patronymic, $arr) {

    $fullname = mb_convert_case(getFullnameFromParts($surname, $name, $patronymic), MB_CASE_TITLE);
    
    $gender = getGenderFromName($fullname);

    //10 попыток выбора рандомного пользователя противоположного пола
    for ($i=0; $i < 10; $i++) {

        $rand_usr = $arr[rand(0, count($arr)-1)]['fullname'];
        if (getGenderFromName($rand_usr)!=$gender) break;
    
    }

    $result = getShortName($fullname) . ' + ' .getShortName($rand_usr). ' = <br>';
    $result .= '♡ Идеально на '.rand(50,100).'% ♡';
    
    return $result;

}

//Проверка функций работы с подстроками
// foreach ($example_persons_array as $key => $user) {

//     $parts = getPartsFromFullname($user['fullname']);
    
//     echo $user['fullname'] . ', ' . $user['job']; //Иванов Иван Иванович, tester
//     echo "<br>getFullnameFromParts: " . getFullnameFromParts($parts['surname'], $parts['name'], $parts['patronymic']); //Иванов Иван Иванович
//     echo "<br>getShortName: " . getShortName($user['fullname']); //Иван И.
//     echo "<br>getGenderFromName: " . getGenderFromName($user['fullname']); //1
//     echo "<hr><br>";

// }

//Проверка функции со статистикой полов
echo getGenderDescription($example_persons_array)."<hr><br>";

//Проверка функции подбора партнера
echo getPerfectPartner("ИванОв", "ИвАн", "ивановИЧ", $example_persons_array)."<hr><br>";