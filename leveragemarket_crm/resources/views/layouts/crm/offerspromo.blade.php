<div class="container-fluid pb-3">
    <div class="row g-4">
        <!-- Banner Slider -->
        <div class="col-lg-9 col-md-8">
            <div class="banner-slider">
                <div class="slider-container">
                    <button class="slider-btn prev-btn" id="bannerPrev">
                        <svg version="1.1" width="30px" height="30px" viewBox="0 0 64 64">
                            <polyline fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="bevel"
                                points="37,15 20,32 37,49" />
                        </svg>
                    </button>

                    <div class="slider-images" id="bannerSlider">
                        @foreach ($promodata as $key => $promo)
                            <div class="slider-image {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/promo/' . $promo->promo_image) }}"
                                    alt="Promotion {{ $key + 1 }}">
                            </div>
                        @endforeach

                        @if ($promodata->count() == 0)
                            <div class="slider-image active">
                                <img src="/assets/images/banner-default.png" alt="Default Banner">
                            </div>
                        @endif
                    </div>

                    <button class="slider-btn next-btn" id="bannerNext">
                        <svg version="1.1" width="30px" height="30px" viewBox="0 0 64 64">
                            <polyline fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="bevel"
                                points="27,15 44,32 27,49" />
                        </svg>
                    </button>
                </div>
                {{-- <div class="slider-dots" id="bannerDots"></div> --}}
            </div>
        </div>

        <!-- Bonus Slider -->
        <div class="col-lg-3 col-md-4">
            <div class="bonus-slider">
                <div class="bonus-label">
                    <span>BONUS OFFERS</span>
                </div>
                <div class="slider-container">
                    <!--<button class="slider-btn prev-btn small" id="bonusPrev">
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="25px" height="25px"
                                        viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                        <g>
                                            <polyline fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="bevel"
                                                stroke-miterlimit="10" points="37,15 20,32 37,49 	" />
                                        </g>
                                    </svg>
                                </button>-->

                    <div class="slider-images" id="bonusSlider">
                        @foreach ($bonusesdata as $key => $bonus)
                            @if (!empty($bonus->bonus_images) && $bonus->bonus_images != 'null')
                                <div class="slider-image {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/bonus/' . $bonus->bonus_images) }}"
                                        alt="Bonus {{ $key + 1 }}">
                                </div>
                            @else
                                <div class="slider-image {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('images/placeholder.png') }}" alt="Bonus {{ $key + 1 }}">
                                </div>
                            @endif
                        @endforeach

                        @if ($bonusesdata->count() == 0)
                            <div class="slider-image active">
                                <img src="/assets/images/placeholder.png" alt="Default Banner">
                            </div>
                        @endif
                    </div>

                    <!--<button class="slider-btn next-btn small" id="bonusNext">
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="25px" height="25px"
                                        viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                        <g>
                                            <polyline fill="none" stroke="#fff" stroke-width="2" stroke-linejoin="bevel"
                                                stroke-miterlimit="10" points="27,15 44,32 27,49 	" />
                                        </g>
                                    </svg>
                                </button>-->
                </div>


            </div>
        </div>
    </div>
</div>
