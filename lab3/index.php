<?php

$day = date("N");

//John Styles
if ($day == 1 || $day == 3 || $day == 5) {
    $john = "8:00 - 12:00";
} else {
    $john = "no work";
}

//Jane Doe
if ($day == 2 || $day == 4 || $day == 6) {
    $jane = "12:00 - 16:00";
} else {
    $jane = "no work";
}

echo "Расписание на " . date('d.m.Y') . "<br><br>";
echo "№ | Фамилия Имя   | График работы<br>";
echo "-----------------------------------<br>";
echo "1 | John Styles   | $john<br>";
echo "2 | Jane Doe      | $jane<br>";

echo "<br />";
echo "goal n 2<br />";

$a = 0;
$b = 0;

for ($i = 0; $i <= 5; $i++) {
   $a += 10;
   $b += 5;

   echo "Шаг $i: a = $a, b = $b <br>";
}

echo "End of the loop: a = $a, b = $b";

//Через While


$a = 0;
$b = 0;
$i = 0;

while ($i <= 5) {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
}

echo "End of the loop: a = $a, b = $b";

//Через do-while

$a = 0;
$b = 0;
$i = 0;

do {
    $a += 10;
    $b += 5;

    echo "Шаг $i: a = $a, b = $b <br>";

    $i++;
} while ($i <= 5);

echo "End of the loop: a = $a, b = $b";