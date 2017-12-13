canvas               = O('logo')
context              = canvas.getContext('2d')
image                = new Image()
image.src            = 'vfacebookte.jpg'

image.onload = function()
{
  context.beginPath();	//рисуем синий прямоугольник на заднем фоне
  context.rect(0, 0, 700, 100);
  context.fillStyle = '#365295';
  context.fill();
  context.drawImage(image, 30, -105)  //помещаем картинку
}

function O(i)	//объект по айди
{
  if (typeof i == 'object') return i
  else return document.getElementById(i)
}

function S(i)	//свойства объекта
{
  return O(i).style
}

function C(n)	//класс
{
  var t = document.getElementsByTagName('*')
  var o = []

  for (var i in t)
  {
    var e = typeof t[i] == 'object' ? t[i].className : ''
    
    if (e                        ==  n ||
        e.indexOf(' ' + n + ' ') != -1 ||
        e.indexOf(      n + ' ') ==  0 ||
        e.indexOf(' ' + n      ) == (e.length - n.length - 1))
          o.push(t[i])
  }

  return o
}

//Функция js показать/скрыть пароль
function Show_HidePassword(id, id1)
{
	var element = document.getElementById(id);
	if (element.type == 'password')
	{
		var inp = document.createElement("input");
		inp.id = id;
		inp.type = "text";
		inp.name = "pass";
		inp.value = element.value;
		element.parentNode.replaceChild(inp, element);
		Show_HidePasswordRepeat(id1); //показать/скрыть повторенный пароль
	}
	else {
		var inp = document.createElement("input");
		inp.id = id;
		inp.type = "password";
		inp.name = "pass";
		inp.value = element.value;
		element.parentNode.replaceChild(inp, element);
		Show_HidePasswordRepeat(id1); //показать/скрыть повторенный пароль
	}
}

//Функция js показать/скрыть повторенный пароль
function Show_HidePasswordRepeat(id)
{
	var element = document.getElementById(id);
	if (element.type == 'password')
	{
		var inp = document.createElement("input");
		inp.id = id;
		inp.type = "text";
		inp.name = "passrepeat";
		inp.value = element.value;
		element.parentNode.replaceChild(inp, element);
	}
	else {
		var inp = document.createElement("input");
		inp.id = id; 
		inp.type = "password"; 
		inp.name = "passrepeat"; 
		inp.value = element.value; 
		element.parentNode.replaceChild(inp, element);
	}
}