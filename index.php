<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Gym bridges</title>
  <!-- Подключение CSS файла -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <link rel="icon" href="Logo1.svg" type="image/x-icon">
  <link rel="stylesheet" type="text/css" href="style.css">

  <script type="text/javascript" src="jquery-1.6.4.min.js"></script>
  <script type="text/javascript" src="jquery.maphilight.js"></script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaLxpPOdyV8bVweF1y0AQRAtLPdfftFvs&callback=initMap"></script>
  <script type="text/javascript" src="mapscript.js"></script>

  <script type="text/javascript">
  $(function() {
          $('.map').maphilight();

          $('#squidheadlink').mouseover(function(e) {
              $('#squidhead').mouseover();
          }).mouseout(function(e) {
              $('#squidhead').mouseout();
          }).click(function(e) { e.preventDefault(); });

          
            // Initialize map on load
          if ($('#map').length) {
              initMap();
          }

          // Reinitialize map when home link is clicked
          $('a[href="index.html"]').click(function() {
              setTimeout(function() {
                  if ($('#map').length) {
                      initMap();
                  }
              }, 100);
          });
  });
</script>
<style>
    .swiper-slide img {
      display: block;      /* Устраняет отступы по умолчанию от inline элементов */
      width: 100%;         /* Заставляет картинку занимать весь доступный ширину */
      height: 100%;        /* Поддерживает пропорции изображения */
      margin: 0;           /* Убирает внешние отступы */
      padding: 0;          /* Убирает внутренние отступы */
      object-fit: cover;     /* Обрезает изображение, сохраняя его центральные части */
     object-position: bottom; /* Приоритет нижней части изображения */
    
}
.swiper-slide{
  height: 600px;
  position: relative
}
.slider .container {
  width: 100%;      /* Ensures container is full width */
  max-width: none;  /* Removes any maximum width restrictions */
  padding: 0;       /* Removes padding that might cause issues */
  margin-right: 0;
  margin-left: 0;
  height: 600px;    /* Specific height for sliders */
}
 .slider .row {
  width: 100%;      /* Ensures container is full width */
  max-width: none;  /* Removes any maximum width restrictions */
  padding: 0;       /* Removes padding that might cause issues */
  margin-right: 0;
  margin-left: 0;
  height: 600px;    /* Specific height for sliders */
}


.slide-caption {
  position: absolute;
  top: 40%;
  left: 10%;
  color: #fff;
  z-index: 2;
  text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
}
.slide-caption h2 {
  font-size: 2.5rem;
  font-weight: bold;
}
.slide-caption .btn {
  margin-top: 15px;
}



.muscle-map {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center; /* Для центровки текста внутри */
}

.muscle-text{
 

  color: #0D1C2E;
  font-family: Anton;
  font-size: 36px;
  font-style: normal;
  font-weight: 400;
  line-height: normal;
  letter-spacing: 1.8px;
}

.col-12.col-lg-6 img {
  max-width: 470px; /* Максимальная ширина изображения */
  height: auto;
}
.img-fluid.map.maphilighted{
  max-width: 470px; /* Максимальная ширина изображения */
  height: 100%;
  margin-top: 0px !important;
}

.muscle-map-section {
  background: linear-gradient(to bottom right, #f8f9fa, #e9ecef);
  animation: fadeInUp 0.6s ease-in-out;
}

.container.bg-light {
  background-color: #ffffff !important;
  border: 1px solid #dee2e6;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}


.fade-in {
  opacity: 0;
  transform: translateY(40px);
  animation: fadeInUp 1s ease-out forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}


</style>
</head>
<body>
<?php include 'includes/avatar_loader.php'; ?> 
<?php include 'includes/header.php'; ?>
  

<main>

<section class="slider">
  <div class="container">
    <div class="row">
      <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide"><img src="images/foto1.jpg" alt="">

            <div class="slide-caption">
              <h2>Achieve Your Fitness Goals</h2>
              <a href="exercises_page.php" class="btn btn-outline-light">Explore Exercises</a>
            </div>

          </div>
          <div class="swiper-slide"><img src="images/foto2.jpg" alt="">

            <div class="slide-caption">
              <h2>Learn More About Us</h2>
              <a href="aboutus.html" class="btn btn-outline-light">Read Our Story</a>
            </div>

          </div>
          <div class="swiper-slide"><img src="images/foto3.jpg" alt="">
          
            
            <div class="slide-caption">
              <h2>Join Our Community</h2>
              <a href="register.php" class="btn btn-outline-light">Sign Up Now</a>
            </div>

          </div>
          <div class="swiper-slide"><img src="images/foto4.jpg" alt="">
        
            <div class="slide-caption">
              <h2>Find the Nearest Gym</h2>
              <a href="#map" class="btn btn-outline-light">Open Map</a>
            </div>
        
          </div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
</section>





<section class="muscle-map-section py-5" id="muscle-map-section">
  <div class="container bg-white shadow rounded p-4 fade-in">
    <h2 class="text-center muscle-text mb-4">
      <i class="bi bi-person-bounding-box me-2"></i>Muscle selection
    </h2>
    <div class="row justify-content-center">
      <div class="col-12 col-lg-6 text-center mb-3">
        <img src="download 1body_smaller_front.png" class="img-fluid map maphilighted" alt="Front View" usemap="#muscle-map-front">
      </div>
      <div class="col-12 col-lg-6 text-center">
        <img src="download 1body_smaller_back.png" class="img-fluid map maphilighted" alt="Back View" usemap="#muscle-map-back">
      </div>
    </div>
    <div class="text-center mt-4">
      <a href="exercises_page.php?category=Arms" class="btn btn-outline-primary me-2 d-inline-flex align-items-center">
        <img src="images/icons/biceps.png" alt="Arms" width="20" height="20" class="me-2"> Arms
      </a>
      <a href="exercises_page.php?category=Chest" class="btn btn-outline-danger me-2 d-inline-flex align-items-center">
        <img src="images/icons/body.png" alt="Chest" width="20" height="20" class="me-2"> Chest
      </a>
      <a href="exercises_page.php?category=Abs" class="btn btn-outline-secondary me-2 d-inline-flex align-items-center">
        <img src="images/icons/abs.png" alt="Abs" width="20" height="20" class="me-2"> Abs
      </a>
      <a href="exercises_page.php?category=Back" class="btn btn-outline-warning me-2 d-inline-flex align-items-center">
        <img src="images/icons/body-part.png" alt="Back" width="20" height="20" class="me-2"> Back
      </a>
      <a href="exercises_page.php?category=Legs" class="btn btn-outline-success me-2 d-inline-flex align-items-center">
        <img src="images/icons/leg.png" alt="Legs" width="20" height="20" class="me-2"> Legs
      </a>
    </div>


  </div>



  
<map name="muscle-map-front">
<area href="exercises_page.php?muscle=Forearms" title="left-forearm" shape="poly" coords="75,365,80,353,86,338,92,326,98,306,104,293,112,285,122,275,124,287,125,295,134,293,145,288,152,286,152,295,148,308,143,318,135,327,124,336,113,346,105,355,99,364,96,371,92,378" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Biceps" title="left-biceps" shape="poly" coords="127,298,125,286,123,272,125,259,129,246,134,234,143,230,151,223,160,219,167,223,172,230,170,239,169,250,167,261,164,272,159,281,151,287,142,290,134,294" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Shoulders" title="left-sholder" shape="poly" coords="137,229,134,216,134,202,137,191,143,179,151,173,161,168,171,167,184,168,192,169,200,175,191,179,183,186,174,198,166,210,161,219,154,220,146,225" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Chest Muscles" title="left-chest" shape="poly" coords="160,218,163,212,168,205,173,199,177,192,183,186,188,181,194,178,200,176,206,177,211,179,216,181,221,184,226,188,229,193,231,199,231,205,231,211,232,216,232,223,231,230,226,234,219,236,211,236,205,238,198,238,191,236,183,234,175,231,167,225" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Chest Muscles" title="right-chest" shape="poly" coords="310,219,305,223,299,227,293,231,287,235,280,237,272,239,264,238,255,236,247,236,240,233,238,225,239,217,239,209,239,201,241,195,244,190,249,187,253,183,258,179,263,177,269,175,274,176,279,179,284,183,289,187,293,194,297,198,300,203,303,207,306,211" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Shoulders" title="right-shoulder" shape="poly" coords="273,174,278,169,283,168,289,167,295,167,302,167,307,168,314,170,319,172,323,175,327,179,331,183,333,187,335,192,335,198,336,203,336,208,336,215,336,220,335,226,332,231,325,224,319,219,312,219,307,213,302,205,298,197,291,189,285,182,278,177" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Biceps" title="right-biceps" shape="poly" coords="302,227,310,220,316,218,330,228,335,230,339,234,345,244,348,257,349,268,349,275,348,287,346,296,331,291,320,286,313,279,309,270,306,257,303,249,303,238" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Forearms" title="right-forearm" shape="poly" coords="347,295,350,274,356,280,362,288,370,297,374,306,377,316,380,327,384,337,389,347,392,356,395,365,378,378,376,367,368,357,359,346,348,335,336,326,327,312,322,298,319,284" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Abdominal Muscles" title="abdominal-muscles" shape="poly" coords="199,240,219,236,230,234,237,235,243,234,252,236,263,238,270,242,271,251,271,259,271,266,271,272,271,278,271,286,272,294,271,302,269,310,268,319,266,324,265,330,264,338,264,346,262,355,259,365,257,374,254,382,250,392,247,400,240,404,231,404,222,400,216,391,212,380,208,368,205,356,203,343,202,331,201,322,198,314,196,305,196,292,196,275,197,256" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Lats" title="left latissimus muscle and oblique muscles" shape="poly" coords="175,348,174,332,176,313,178,293,179,276,173,259,168,250,169,236,169,226,177,229,184,232,191,235,198,238,198,250,197,259,197,268,197,278,197,289,197,299,198,311,201,320,202,328,203,337,206,348,207,358,209,367,211,374,214,381,213,386,204,376,196,365,186,357" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Lats" title="right latissimus muscle and oblique muscles" shape="poly" coords="268,240,281,238,293,232,303,226,303,234,305,244,305,252,299,260,295,270,293,284,295,296,297,312,298,326,297,340,297,349,286,359,277,366,270,372,262,383,260,388,253,396,253,386,255,379,260,368,263,356,265,340,269,321,272,308,273,288,272,270,271,252" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Quadriceps" title="left quadriceps" shape="poly" coords="175,350,182,354,184,362,184,372,183,381,185,388,189,399,191,410,195,424,199,440,203,461,206,473,208,486,210,498,210,511,210,518,209,529,199,522,190,512,183,500,181,508,179,514,174,518,169,509,166,500,163,491,160,481,156,473,155,459,155,444,156,428,157,416,159,402,162,387,165,376,170,362" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Quadriceps" title="right quadriceps" shape="poly" coords="297,350,301,358,304,368,307,379,311,392,314,408,315,420,317,430,317,442,319,456,317,469,314,478,309,491,305,502,302,510,297,519,291,515,290,503,286,508,283,514,277,520,271,526,262,527,261,514,261,500,265,480,269,459,275,438,278,421,283,404,289,382,287,369,289,355" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Adductors" title="left-adductor muscle" shape="poly" coords="184,356,191,360,199,367,205,376,214,386,219,394,225,401,230,410,229,418,229,434,227,450,225,462,225,474,221,486,218,501,212,515,210,498,209,481,205,462,200,442,196,421,191,404,187,390,182,384,184,368" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Adductors" title="right-adductor muscle" shape="poly" coords="289,355,281,360,272,368,266,376,260,384,254,392,250,398,245,404,243,410,242,420,243,430,244,440,246,448,248,458,249,468,250,479,252,487,254,495,257,503,261,512,263,501,265,486,267,475,269,462,271,448,277,432,281,416,284,399,290,386,289,370" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="https://www.hbo.com/game-of-thrones" title="Lannister" shape="poly" coords="210,95,207,86,206,77,207,67,210,61,215,52,221,52,226,48,231,48,237,48,242,50,249,52,255,56,260,62,265,66,267,75,267,82,267,88,266,94,261,86,261,77,259,70,255,64,249,64,241,66,232,68,223,68,216,67,213,72,212,78,213,86" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:6,&quot;fillColor&quot;:&quot;FFD700&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Calves" title="left-calf muscle" shape="poly" coords="158,697,158,683,154,664,151,643,149,617,153,593,161,554,161,601,162,623,165,648,174,673,175,681,176,665,177,634,181,610,191,583,201,563,203,593,201,609,195,631,187,653,178,675,181,699" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Calves" title="right-calf muscle" shape="poly" coords="314,693,315,676,321,661,324,625,319,596,311,553,312,572,311,595,311,618,305,648,300,684,294,683,295,660,295,634,289,607,279,575,270,560,271,584,273,614,281,640,291,669,294,693" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Upper Trapezius" title="trapezius muscles l" shape="poly" coords="229,175,217,172,205,169,193,168,181,166,188,163,197,160,205,156,210,148,215,137,219,149,223,160" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Upper Trapezius" title="trapezius muscles r" shape="poly" coords="259,135,261,147,266,155,274,160,282,164,289,166,277,168,268,169,261,170,253,172,243,176,247,166,252,154,255,144" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
</map>

<map name="muscle-map-back">
<area href="exercises_page.php?muscle=Triceps" title="left-tricpes" shape="poly" coords="113,287,108,276,108,263,113,247,119,231,125,217,137,207,154,200,156,209,159,219,161,232,158,245,153,259,148,272,137,288,125,288" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:7,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Shoulders" title="left-shoulder" shape="poly" coords="125,217,124,205,126,188,131,173,142,163,156,158,169,156,183,156,187,162,184,168,176,177,165,181,159,186,155,195,152,201,143,204,134,210" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Upper Trapezius" title="Upper trapezius muscles" shape="poly" coords="185,154,193,148,204,140,212,132,217,122,224,123,230,123,237,123,244,121,249,127,252,133,260,141,266,147,276,153,267,152,255,149,242,146,231,143,221,145,211,149,200,151" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Shoulders" title="right-shoulder" shape="poly" coords="272,165,278,156,288,154,299,156,307,158,316,161,323,166,328,171,332,178,335,186,337,196,337,207,336,218,330,209,322,204,314,203,308,198,306,190,300,182,291,178,280,170" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Forearms" title="left-forearm" shape="poly" coords="61,384,67,371,72,349,81,328,90,302,99,286,107,275,113,287,121,288,131,285,138,285,133,304,126,319,117,332,108,345,100,359,92,372,85,388,72,389" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Triceps" title="right-triceps" shape="poly" coords="307,201,315,203,323,205,330,210,334,216,339,225,344,237,349,248,352,260,352,271,350,280,346,287,336,288,326,286,316,275,308,257,302,238,302,225,304,210" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Forearms" title="right-forearm" shape="poly" coords="326,288,334,287,343,287,348,285,353,275,358,281,363,289,368,299,373,309,377,320,382,333,386,344,389,356,393,368,398,382,392,385,384,387,376,387,372,378,368,370,364,362,359,355,352,343,344,333,337,325,332,314,328,302" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Teres major" title="Left teres major" shape="poly" coords="157,191,163,182,174,178,183,169,189,163,194,170,198,180,200,190,201,200,202,208,202,217,195,217,189,221,183,225,174,228,165,224,158,218,155,209,154,201" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Teres major" title="Right teres major" shape="poly" coords="260,217,261,205,262,191,265,179,268,170,273,163,281,171,288,178,296,181,303,185,307,193,306,202,306,212,303,221,294,227,284,227,272,219" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Lats" title="lats muscle" shape="poly" coords="159,217,165,223,173,224,184,221,195,216,201,214,206,224,212,234,217,245,224,256,227,263,233,266,238,259,243,250,246,241,252,231,259,222,260,216,266,217,274,220,282,225,292,225,300,222,300,231,299,240,298,251,297,261,296,271,294,280,289,289,284,299,276,307,273,317,269,326,269,335,262,327,260,316,257,304,255,291,249,279,238,272,229,271,220,275,210,280,205,290,204,301,204,309,201,320,197,329,190,335,190,324,187,313,180,304,173,293,166,284,164,268,161,249,160,233" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Middle/Lower Trapezius" title="Middle/lower trapezius" shape="poly" coords="184,155,194,153,207,149,219,146,230,142,241,145,252,148,261,151,277,154,274,162,268,170,264,181,261,192,259,205,259,217,256,225,251,234,245,245,239,256,232,269,225,262,220,251,214,241,207,231,201,218,201,203,201,188,197,172,189,164" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Lower Back" title="Lower Back" shape="poly" coords="192,335,198,326,203,318,204,307,203,295,206,285,212,280,219,275,227,268,234,269,239,271,246,275,252,281,256,289,256,298,256,305,258,314,261,321,264,328,270,337,260,339,251,343,242,349,236,355,230,363,224,354,216,347,205,341" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Glutes" title="glutes" shape="poly" coords="197,338,206,340,213,345,220,351,226,357,229,365,234,359,238,352,246,345,256,341,265,337,270,343,276,348,282,354,286,360,290,369,291,376,294,383,298,389,299,394,300,400,299,409,298,417,294,424,286,429,279,435,272,435,262,435,252,436,244,435,236,431,237,421,231,409,228,415,226,422,225,431,219,435,210,436,200,436,187,435,176,431,168,424,162,413,161,398,162,389,170,380,170,368,176,357,187,346" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Calves" title="left-calves" shape="poly" coords="146,547,151,544,157,541,162,547,166,560,169,551,176,559,180,567,184,576,182,589,180,603,180,615,180,626,177,636,173,648,166,645,158,639,150,627,145,633,138,639,129,643,128,630,129,615,132,603,135,593,140,584,144,574,146,559" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Calves" title="right-calves" shape="poly" coords="278,576,282,566,285,557,290,550,293,559,296,549,303,538,308,540,312,544,314,551,313,559,314,567,317,575,320,583,325,590,328,599,329,612,330,623,331,633,332,642,324,641,316,634,310,626,306,634,302,641,296,645,288,649,282,638,281,625,281,613,281,598,280,587" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Adductors" title="Inner thighs aductors left" shape="poly" coords="194,436,196,448,199,460,199,472,201,485,202,496,206,507,212,495,217,482,222,468,224,453,224,434,214,436,206,438" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Adductors" title="Inner thighs aductors right" shape="poly" coords="237,434,236,445,236,456,240,466,244,478,248,488,254,508,258,493,261,479,264,463,266,447,267,437,256,438,248,438" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Hamstrings" title="left hamstrings" shape="poly" coords="162,393,156,401,152,412,151,425,152,436,151,451,151,466,152,483,155,496,155,512,152,525,149,545,158,540,164,533,170,526,171,538,170,548,174,555,179,563,183,572,185,578,190,570,194,561,199,553,201,542,204,530,206,518,207,509,202,498,201,486,201,475,200,463,197,450,195,438,188,436,180,434,172,430,165,423,163,413,163,405" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
<area href="exercises_page.php?muscle=Hamstrings" title="right hamstrings" shape="poly" coords="300,396,306,402,308,412,310,425,309,438,309,452,310,466,308,482,308,497,308,510,308,524,313,545,303,538,294,530,291,542,291,551,286,557,282,566,277,577,270,566,265,552,260,536,256,521,254,509,258,500,259,486,261,473,264,458,268,436,276,436,284,432,293,427,298,418,300,406" alt="Link" data-maphilight="{&quot;strokeColor&quot;:&quot;ffffff&quot;,&quot;strokeWidth&quot;:8,&quot;fillColor&quot;:&quot;6AA8FE&quot;,&quot;fillOpacity&quot;:0.9}" title="Metadata&#39;d up a bit">
</map>
</section>


<!-- Секция карты -->
<section id="map-section" class="bg-light py-5 fade-in">
  <div class="container text-center mb-4">
    <h2 class="fw-bold" style="font-family: 'Anton', sans-serif; font-size: 36px;">
      <i class="bi bi-geo-alt-fill me-2"></i>Find Your Nearest Gym
    </h2>
    <p class="text-muted">Explore gyms around you on the map below</p>
  </div>

  <div class="container">
    <div id="map" style="height: 600px; border-radius: 12px; box-shadow: 0 5px 30px rgba(0,0,0,0.2);"></div>
  </div>
</section>



</main>


  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper(".mySwiper", {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: true,
      autoplay: {
        delay: 7500,               // Задержка в 2500 миллисекунд (2.5 секунды)
        disableOnInteraction: false // Продолжать автопроигрывание после взаимодействия
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });

  </script>

<script>
  function scrollToMuscleMap() {
    const el = document.getElementById("muscle-map");
    if (el) {
      const yOffset = 150; // сместиться НИЖЕ
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: y, behavior: "smooth" });
    }
  }

  // Обработка перехода с других страниц (по хэшу)
  document.addEventListener("DOMContentLoaded", function () {
    if (window.location.hash === "#muscle-map") {
      history.replaceState(null, null, ' ');
      setTimeout(scrollToMuscleMap, 300);
    }
  });

  // Обработка нажатия, если уже на index.php
  document.getElementById("muscle-scroll-link")?.addEventListener("click", function (e) {
    if (window.location.pathname.endsWith("index.php")) {
      e.preventDefault(); // отменим обычный переход
      scrollToMuscleMap();
    }
  });
</script>




<?php include 'includes/footer.php'; ?>
 
</body>
</html>