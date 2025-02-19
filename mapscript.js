function initMap() {
    // Создайте объект карты
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10, // Уровень масштабирования карты
      center: { lat: 56.95407174960792, lng: 24.0790048368174 } // Координаты центра карты 
    });

    // GYM! Latvia Imanta
    var marker = new google.maps.Marker({
      position: { lat: 56.957335952569, lng: 24.03638255128942 }, // Координаты места 
      map: map,
      title: 'GYM! Latvia Imanta' // Заголовок метки
    });

    // Lemon Gym Imanta
    var marker = new google.maps.Marker({
        position: { lat: 56.96011008953729, lng: 24.034244565966507 }, // Координаты места  
        map: map,
        title: 'Lemon Gym Imanta' // Заголовок метки
    });

    // MyFitness Imanta
    var marker = new google.maps.Marker({
        position: { lat: 56.96049712430333, lng: 24.01253163745811 }, // Координаты места  
        map: map,
        title: 'MyFitness Imanta' // Заголовок метки
    });

    // Gym! Riga Olimpia
    var marker = new google.maps.Marker({
        position: { lat: 56.94998545615211, lng: 24.082447642119416 }, // Координаты места   
        map: map,
        title: 'Gym! Riga Olimpia' // Заголовок метки
    });

    // Gym! Riga Origo
    var marker = new google.maps.Marker({
        position: { lat: 56.94703617806939, lng: 24.118820868701736 }, // Координаты места
        map: map,
        title: 'Gym! Riga Origo' // Заголовок метки
    });

    // MyFitness
    var marker = new google.maps.Marker({
        position: { lat: 56.92804781964521, lng: 24.10127400699908 }, // Координаты места 
        map: map,
        title: 'MyFitness Rīga Plaza' // Заголовок метки
    });
    
    // MyFitness Alfa
    var marker = new google.maps.Marker({
        position: { lat: 56.983929422364795, lng: 24.203755845935998 }, // Координаты места 
        map: map,
        title: 'MyFitness Alfa' // Заголовок метки
    });


    // MyFitness Zolitūde
    var marker = new google.maps.Marker({
        position: { lat: 56.943471973362456, lng: 24.01812006948449 }, // Координаты места  
        map: map,
        title: 'MyFitness Zolitūde' // Заголовок метки
    });

     // Lemon Gym Kengarags
    var marker = new google.maps.Marker({
        position: { lat: 56.90825301735668, lng: 24.1826950443047 }, // Координаты места  
        map: map,
        title: 'Lemon Gym Kengarags' // Заголовок метки
    });

      // Lemon Gym Akropole
    var marker = new google.maps.Marker({
        position: { lat: 56.92439546166249, lng: 24.173955797506686 }, // Координаты места 
        map: map,
        title: 'Lemon Gym Akropole' // Заголовок метки
    });

      // Lemon Gym Pļavnieki
    var marker = new google.maps.Marker({
        position: { lat: 56.94708874759651, lng: 24.20958899329813 }, // Координаты места  
        map: map,
        title: 'Lemon Gym Pļavnieki' // Заголовок метки
    });

      // Lemon Gym Purvciems
    var marker = new google.maps.Marker({
        position: { lat: 56.95897740415476, lng: 24.189847939047183 }, // Координаты места 
        map: map,
        title: 'Lemon Gym Purvciems' // Заголовок метки
    });

      // Lemon Gym Skanste
    var marker = new google.maps.Marker({
        position: { lat: 56.9664380561771, lng: 24.11923074398969 }, // Координаты места  
        map: map,
        title: 'Lemon Gym Skanste' // Заголовок метки
    });

      // Lemon Gym Teika
    var marker = new google.maps.Marker({
        position: { lat: 56.97308148781072, lng: 24.166780943656963 }, // Координаты места  
        map: map,
        title: 'Lemon Gym Teika' // Заголовок метки
    });

   // Lemon Gym Jugla
    var marker = new google.maps.Marker({
        position: { lat: 56.99042037425076, lng: 24.243778579799965 }, // Координаты места 
        map: map,
        title: 'Lemon Gym Jugla' // Заголовок метки
    });   

   // MyFitness Spice
    var marker = new google.maps.Marker({
        position: { lat: 56.93393544126149, lng: 24.040473757964598 }, // Координаты места  
        map: map,
        title: 'MyFitness Spice' // Заголовок метки
    });

   // MyFitness Aleja
    var marker = new google.maps.Marker({
        position: { lat: 56.897795018698204, lng: 24.078982560043528 }, // Координаты места 
        map: map,
        title: 'MyFitness Aleja' // Заголовок метки
});

   // MyFitness Galleria Riga
    var marker = new google.maps.Marker({
        position: { lat: 56.95617636324165, lng: 24.12069956162377 }, // Координаты места 
        map: map,
        title: 'MyFitness Galleria Riga' // Заголовок метки
});

   // MyFitness Upītis
    var marker = new google.maps.Marker({
        position: { lat: 56.95158950592587, lng: 24.129797614023012 }, // Координаты места 
        map: map,
        title: 'MyFitness Upītis' // Заголовок метки
});

   // MyFitness Matīss
    var marker = new google.maps.Marker({
        position: { lat: 56.95945234432412, lng: 24.133574164075526 }, // Координаты места 
        map: map,
        title: 'MyFitness Matīss' // Заголовок метки
});

   // MyFitness Domina
    var marker = new google.maps.Marker({
        position: { lat: 56.96723643392068, lng: 24.16443897730737 }, // Координаты места 
        map: map,
        title: 'MyFitness Domina' // Заголовок метки
});

   // MyFitness SkyandMore
    var marker = new google.maps.Marker({
        position: { lat: 56.9864092442013, lng: 24.132709070658162 }, // Координаты места 
        map: map,
        title: 'MyFitness SkyandMore' // Заголовок метки
});

   // MyFitness Dzelzava
    var marker = new google.maps.Marker({
        position: { lat: 56.955485377600645, lng: 24.200996169208405 }, // Координаты места 
        map: map,
        title: 'MyFitness Dzelzava' // Заголовок метки
});

   // MyFitness Sāga
    var marker = new google.maps.Marker({
        position: { lat: 56.9537176620398, lng: 24.252289215317205 }, // 
        map: map,
        title: 'MyFitness Sāga' // Заголовок метки
});

   // Olymp
    var marker = new google.maps.Marker({
        position: { lat: 56.954240322510984, lng: 24.153995096905454 }, // 
        map: map,
        title: 'Olymp' // Заголовок метки
});

   // Powerlab Fitness
    var marker = new google.maps.Marker({
        position: { lat: 56.97579168734547, lng: 23.7974861460166 }, // 
        map: map,
        title: 'Powerlab Fitness' // Заголовок метки
});

   // Fitnesa Centrs SIENA
    var marker = new google.maps.Marker({
        position: { lat: 56.95673235346234, lng: 23.611422737786903 }, // 
        map: map,
        title: 'Fitnesa Centrs SIENA' // Заголовок метки
});

   // RMI GYM
    var marker = new google.maps.Marker({
        position: { lat: 56.794216026814475, lng: 23.938560246442623 }, // 
        map: map,
        title: 'RMI GYM' // Заголовок метки
});

    // Gym "OC Ventspils"
    var marker = new google.maps.Marker({
        position: { lat: 57.38688678703296, lng: 21.57073903188825 }, // 
        map: map,
        title: 'Gym "OC Ventspils"' // Заголовок метки
});

    // BEST FIT Fitnesa klubs
    var marker = new google.maps.Marker({
        position: { lat: 55.874483444785334, lng: 26.51698944558017 }, // 
        map: map,
        title: 'BEST FIT Fitnesa klubs' // Заголовок метки
});
    // Добавьте другие метки, используя аналогичный код
  }