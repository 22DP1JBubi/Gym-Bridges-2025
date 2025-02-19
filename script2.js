// document.addEventListener("DOMContentLoaded", function () {
//   const muscleAreas = document.querySelectorAll("area");

//   muscleAreas.forEach((area) => {
//     area.addEventListener("mouseover", function () {
//       area.classList.add("area-highlighted"); /* Добавляем класс для подсветки области */
//     });

//     area.addEventListener("mouseout", function () {
//       area.classList.remove("area-highlighted"); /* Удаляем класс подсветки области */
//     });
//   });
// });


$('muscle-map').maphilight({

  // fill the shape
  fill: true,

  // fill color
  fillColor: 'FF0000',

  // fill opacity
  fillOpacity: 0.2,

  // outline the shape
  stroke: true,

  // stroke color
  strokeColor: 'ff0000',

  // stroke opacity
  strokeOpacity: 1,

  // stroke width
  strokeWidth: 1,

  // fade in the shapes on mouseover
  fade: true,

  // always show the hilighted areas
  alwaysOn: true,

  // never show the hilighted areas
  neverOn: false,

  // The name of an attribute to group areas by, or a selector for elements in the map to group. 
  // Or an array of the same
  // If this is present then all areas in the map which have the same attribute value as the hovered area will hilight as well
  groupBy: false,

  // If true, applies the class on the <img> to the wrapper div maphilight created.
  // If a string, that string is used as a class on the wrapper div.
  wrapClass: true,
  
  // apply a shadow to the shape
  shadow: true,
  shadowX: 0,
  shadowY: 0,
  shadowRadius: 6,
  shadowColor: 'FF0000',
  shadowOpacity: 0.8,
  // Can be 'outside', 'inside', or 'both'.
  shadowPosition: 'outside',
  // Can be 'stroke' or 'fill'
  shadowFrom: true,
  
});

