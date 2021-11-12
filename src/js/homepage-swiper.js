import Swiper, { Navigation, Autoplay } from 'swiper';

export function homepageSwiperInit() {
  console.log('homepage swiper initiated');
  const homepageSwiperElement = document.getElementById('homepage-swiper');

  if(homepageSwiperElement) {
    Swiper.use([Navigation, Autoplay]);
    let portfolioSwiper = new Swiper('#homepage-swiper', {
      direction: 'horizontal',
      loop: true,
      // navigation: {
      //   nextEl: '.portfolio-swiper-button-next',
      //   prevEl: '.portfolio-swiper-button-prev',
      // },
      slidesPerView: 1,
      observer: true,
      observeParents: true,
      autoplay: {
        delay: 5000,
      },
    });
  }
}