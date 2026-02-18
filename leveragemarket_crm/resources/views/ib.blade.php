@extends('layouts.crm.crm')
@section('content')
<div class="pc-container">
    <div class="pc-content">
		<div class="row">
			<div class="col-12 text-center">
				<div class="page-header mb-0 pb-0 pt-1 mb-3">
					<div class="page-block">
						<div class="row align-items-center">
							<div class="col-md-12">
								<div class="page-header-title h2">
									<h2 class="mb-0 text-center">{{ $pageTitle }}</h2>
								</div>
							</div>
						</div>
					</div>
				</div>
		
				<div class="container mb-3">
					<img src="{{ asset('assets/images/handshake_only.png') }}" alt="IB Partnership" class="img-fluid mb-4" style="max-width:350px;">
					<h4 class="fw-bold mb-3">Join our IB Partnership program!</h4>
					<p class="mb-4 fs-5">Apply and get your personalized partnership conditions.</p>
					@if (is_null($ib_result))
						<a href="javascript:void(0);" class="btn btn-primary ibenrollButton filled" data-bs-toggle="modal" data-bs-target="#ibPartnerModal">Become an IB Partner</a>
					@elseif ($ib_result->status == 0)
						<span class="badge bg-light-warning mt-4 mb-5">Pending Approval</span>
					@endif
					
				</div>
				
				<section id="hero" class="hero-section">   
					<div class="hero-content">     
						<h1 class="hero-title">Become a <span class="text-green">Leverage Markets Partner</span><br>Earn Smarter, Not Harder</h1>     
						<p class="hero-subtitle">Join one of the fastest-growing broker partnership programs.<br>Choose your earning style, <strong>Profit Share up to 45%</strong> or <strong>Volume-Based Rebates</strong>, and start earning <br />instantly from every trade or client success.</p>
					</div>   
					<div class="hero-divider"></div> 
				</section>
			</div>
		</div>
		
		<section id="partnership-models" class="partnership-models-section">
			<div class="container">
				<header class="section-header">
					<h2>Choose Your <span class="text-green">Partnership</span> Model</h2>
					<p>Two powerful ways to earn. Pick what fits your style.</p>
				</header>
				<div class="cards-container">
					<article class="partnership-card">
						<div class="card-header">
							<h3>Profit-Based Partnership<br>Earn up to 45%</h3>
							<p>The smarter way to earn, grow with your traders’ success.</p>
						</div>
						<p class="card-description">Our <strong>Profit Share model</strong> is built for fund managers, experienced affiliates, and long-term partners. You earn <strong>up to 45%</strong> of the net trading revenue from your referred clients, every month.</p>
						<div class="info-box">
							<h4>Example:</h4>
							<p>If your clients generate $10,000 in net revenue, your payout could reach $4,500.</p>
						</div>
						<div class="benefits-container">
							<h4>Why it works:</h4>
							<ul class="benefits-list">
								<li>
									<span class="benefit-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
										</svg>
									</span>
									<span>Scales with performance, the more your clients earn, the more you do</span>             
								</li>
								<li>
									<span class="benefit-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
										</svg>
									</span>
									<span>Transparent calculations and detailed monthly reports.</span>             
								</li>
							  <li>
								 <span class="benefit-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
									   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
									</svg>
								 </span>
								 <span>Ideal for money managers, portfolio introducers, and growth-focused IBs.</span>             
							  </li>
						   </ul>
						</div>
					<hr class="model-divider">
					<div class="tier-model-container flex-row-to-col">
					   <div class="tier-model-text">
						  <h3 class="model-title">                     Tier-Based IB Model — Hierarchical Partner Payout System                   </h3>
						  <p class="model-description">                     Expand your reach and earn from multiple levels of your                     network. Each IB tier receives a share of total net revenue,                     ensuring fair rewards for both direct and indirect                     performance.                   </p>
						  <div class="features-box">
							 <h4 class="features-title">Highlights:</h4>
							 <ul class="features-list">
								<li>
								   <span class="benefit-icon">
									  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										 <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
									  </svg>
								   </span>
								   <span><span class="text-bold">Tier 1 (Sub-IB L2):</span>                           Keeps the main base share (e.g., 30%).</span>                       
								</li>
								<li>
								   <span class="benefit-icon">
									  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										 <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
									  </svg>
								   </span>
								   <span><span class="text-bold">Tier 2 (Sub-IB L1):</span>                           Earns a 10% override on its downline’s revenue.</span>                       
								</li>
								<li>
								   <span class="benefit-icon">
									  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										 <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
									  </svg>
								   </span>
								   <span><span class="text-bold">Tier 3 (Main IB):</span>                           Earns a 5% override on the entire group’s                           revenue.</span>                       
								</li>
								<li>
								   <span class="benefit-icon">
									  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										 <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
									  </svg>
								   </span>
								   <span>Transparent profit tracking with real-time reporting                           in your Partner Portal.</span>                       
								</li>
							 </ul>
						  </div>
					   </div>
					   <img src="https://my.strivefx.com/static-files/179/11.svg" alt="" class="commission-tree-image">               
					</div>
				 </article>
				 <article class="partnership-card">
					<div class="card-header">
					   <h3>Volume-Based Partnership, Earn per Traded Lot</h3>
					   <p>Earn more from every trade, consistent, instant, and transparent.</p>
					</div>
					<p class="card-description"><strong>Prefer predictable, steady income?</strong><br>Our Volume-Based Partner Program rewards you for every trade your clients make, up to $10 per lot, regardless of whether they win or lose. Every trade counts. The more your clients trade, the more you earn.</p>
					<div class="info-box">
					   <h4>Example:</h4>
					   <p>If your clients trade 100 lots of XAUUSD, you can earn up to $1,000, paid instantly, no profit dependency, no hidden limits.</p>
					</div>
					<div class="benefits-container">
					   <h4>Why Partners Love It:</h4>
					   <ul class="benefits-list">
						  <li>
							 <span class="benefit-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							 </span>
							 <span><strong>Earn up to $10/lot</strong> across forex, metals, indices, and crypto.</span>             
						  </li>
						  <li>
							 <span class="benefit-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							 </span>
							 <span><strong>Instant payouts</strong> credited directly to your partner wallet.</span>             
						  </li>
						  <li>
							 <span class="benefit-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							 </span>
							 <span><strong>Scalable income</strong> grow your network, and your rewards grow automatically.</span>             
						  </li>
						  <li>
							 <span class="benefit-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							 </span>
							 <span><strong>Transparent tracking</strong> see every trade and commission in real time.</span>             
						  </li>
						  <li>
							 <span class="benefit-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								   <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							 </span>
							 <span><strong>Perfect for introducers, educators, and influencers</strong>.</span>             
						  </li>
					   </ul>
					</div>
					<hr class="model-divider">
					<div class="tier-model-text">
					   <h3 class="model-title">                   Tier-Based IB Model — Structured Profit Distribution Model                 </h3>
					   <p class="model-description">                   Earn commissions not just from your direct clients, but also                   from your sub-IBs and their networks. Each tier receives its                   own share, ensuring fair distribution and rewarding growth                   across your partner hierarchy.                 </p>
					   <div class="features-box">
						  <h4 class="features-title">Highlights:</h4>
						  <ul class="features-list">
							 <li>
								<span class="benefit-icon">
								   <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
									  <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								   </svg>
								</span>
								<span><span class="text-bold">Tier 1 (Direct IB):</span>                         Keeps the full base rate (e.g., $10/lot).</span>                     
							 </li>
							 <li>
								<span class="benefit-icon">
								   <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
									  <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								   </svg>
								</span>
								<span><span class="text-bold">Tier 2 (Master IB):</span>                         Receives a 25% override on Tier 1’s volume                         ($2.50/lot).</span>                     
							 </li>
							 <li>
								<span class="benefit-icon">
								   <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
									  <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								   </svg>
								</span>
								<span><span class="text-bold">Tier 3 (Super IB or Regional IB):</span>                         Receives 15% override, applicable only when there’s an                         additional layer above.</span>                     
							 </li>
							 <li>
								<span class="benefit-icon">
								   <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
									  <path d="M10.5 12.75L13.5 9.75M13.5 9.75L10.5 6.75M13.5 9.75H6M18.75 9.75C18.75 10.9319 18.5172 12.1022 18.0649 13.1942C17.6126 14.2861 16.9497 15.2782 16.114 16.114C15.2782 16.9497 14.2861 17.6126 13.1942 18.0649C12.1022 18.5172 10.9319 18.75 9.75 18.75C8.5681 18.75 7.39778 18.5172 6.30585 18.0649C5.21392 17.6126 4.22177 16.9497 3.38604 16.114C2.55031 15.2782 1.88738 14.2861 1.43508 13.1942C0.982792 12.1022 0.75 10.9319 0.75 9.75C0.75 7.36305 1.69821 5.07387 3.38604 3.38604C5.07387 1.69821 7.36305 0.75 9.75 0.75C12.1369 0.75 14.4261 1.69821 16.114 3.38604C17.8018 5.07387 18.75 7.36305 18.75 9.75Z" stroke="#00B100" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
								   </svg>
								</span>
								<span><span class="text-bold">Total potential payout:</span>                         Up to <span class="text-bold">$12.50/lot</span> across                         the network, with instant tracking via the Partner                         Portal.</span>                     
							 </li>
						  </ul>
					   </div>
					</div>
					<div class="commission-trees-wrapper">                   <img src="https://my.strivefx.com/static-files/179/%3Csvg.svg" alt="" class="commission-tree-image">               </div>
				 </article>
				</div>
			</div>
		</section>
		
		<section id="comparison" class="comparison-section mt-3">
			<div class="container">
				<header class="section-header">
					<h2>Which Program is <span class="text-green">Right for You?</span></h2>
					<p>Compare both partnership models side by side</p>
				</header>
			  <div class="comparison-grid">
				 <div class="comparison-card">
					<h3 class="text-green">Profit-Based (Up to 45%)</h3>
					<div class="comparison-items-wrapper">
					   <div class="comparison-item">
						  <h4>Best For</h4>
						  <p>Long-term investors, managers</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Earning Type</h4>
						  <p>Performance-based</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Stability</h4>
						  <p>Varies with client results</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Scalability</h4>
						  <p>High (up to 45%)</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Payout Cycle</h4>
						  <p>Monthly</p>
					   </div>
					</div>
				 </div>
				 <div class="comparison-card">
					<h3 class="text-green">Volume-Based (USD/Lot)</h3>
					<div class="comparison-items-wrapper">
					   <div class="comparison-item">
						  <h4>Best For</h4>
						  <p>High-frequency traders, influencers</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Earning Type</h4>
						  <p>Activity-based</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Stability</h4>
						  <p>Fixed per-lot income</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Scalability</h4>
						  <p>Moderate, steady</p>
					   </div>
					   <div class="comparison-item">
						  <h4>Payout Cycle</h4>
						  <p>Monthly</p>
					   </div>
					</div>
				 </div>
			  </div>
			  <div class="info-box tip-box">
				 <h4>Tip:</h4>
				 <p>If you manage high-performing clients, the Profit Share model gives you greater returns.<br>If your clients trade frequently, the Volume Model guarantees steady income.</p>
			  </div>
		   </div>
		</section>
    </div>
  </div>
  
	<div class="modal fade" id="ibPartnerModal" tabindex="-1" aria-labelledby="ibPartnerLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
			  
				<div class="modal-header">
					<h5 class="modal-title text-center" id="ibPartnerLabel">IB Partnership Request</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>

				<form id="ibPartnerForm">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">How long have you worked as an IB and with which broker(s)?</label>
								<input type="text" class="form-control" name="ibExp" required />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">Choose your partnership model</label>
								<select class="form-select" name="partnershipModel" required />
									<option value="Profit-based Model">Profit-based Model</option>
									<option value="Volume-based Model" selected="">Volume-based Model</option>
								</select>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">Which countries or regions do you target?</label>
								<input type="text" class="form-control" name="regions" required />
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">What type of clients do you usually work with?</label>
								<input type="text" class="form-control" name="clientType" required />
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">How many new clients can you bring each month?</label>
								<input type="number" class="form-control" name="clientsbring" required />
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">What is your expected monthly trading turnover?</label>
								<input type="text" class="form-control" name="turnover" />
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">What is the expected monthly deposits from your clients?</label>
								<input type="text" class="form-control" name="deposits" />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">Do you have a website or landing page for promotion?</label>
								<input type="text" class="form-control" name="website" />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">Which marketing channels do you use?</label>
								<input type="text" class="form-control" name="channels" />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">What is your monthly marketing budget?</label>
								<input type="text" class="form-control" name="budget" />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">What is your preferred language for support?</label>
								<input type="text" class="form-control" name="languagePref" />
							</div>
						  
							<div class="col-md-6 mb-3">
								<label class="form-label">Where did you hear about us?</label>
								<input type="text" class="form-control" name="referral" />
							</div>
						  <div class="col-md-6 mb-3">
								<label class="form-label">Investment</label>
								<input type="text" class="form-control" name="investment" />
							</div>
						  
							{{-- <div class="col-md-12 mb-3">
								<label class="form-label">Drop Your Attachement Here</label>
								<input type="file" class="form-control" name="document" />
							</div> --}}
							
						</div>  
						<div class="form-check mb-3">
							<input class="form-check-input" type="checkbox" id="agreeTerms" value="Yes" />
							<label class="form-check-label" for="agreeTerms">
								I confirm that I have reviewed and agree to the full Terms & Conditions.
							</label>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-danger" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-outline-primary" id="submitBtn" disabled >Submit Application</button>
					</div>
				</form>
			</div>
		</div>
	</div>
  
  <script>
	$(function() {
		$('#agreeTerms').on('change', function () {
			if ($(this).is(':checked')) {
				$('#submitBtn').prop('disabled', false);  // Enable button
			} else {
				$('#submitBtn').prop('disabled', true);   // Disable button
			}
		});
		$('#ibPartnerForm').on('submit', function (e) {

			e.preventDefault(); // stop default submit

			// Buttons
			let submitBtn = $('#submitBtn');
			let cancelBtn = $('#cancelBtn');

			// Check if checkbox is ticked
			if (!$('#agreeTerms').is(':checked')) {
				alert("You must agree to the Terms & Conditions before submitting.");
				return false;
			}

			// Disable buttons before submit
			submitBtn.prop('disabled', true).text('Submitting...');
			cancelBtn.prop('disabled', true);

			// Show loading alert
			Swal.fire({
				title: "Submitting...",
				text: "Please wait while we submit your IB application.",
				icon: "info",
				allowOutsideClick: false,
				showConfirmButton: false,
				didOpen: () => Swal.showLoading()
			});

			// Prepare form data
			let formData = new FormData(this);

			$.ajax({
				url: "{{ route('ib-enroll') }}",
				method: "POST",
				data: formData,
				processData: false,
				contentType: false,
				headers: {
					"X-CSRF-TOKEN": "{{ csrf_token() }}"
				},
				success: function (res) {

					$('#ibPartnerModal').modal('hide');
					$('#ibPartnerForm')[0].reset();

					Swal.close(); // close loading modal

					// Check response
					if (res.status === 'true') {
						Swal.fire({
							title: "IB Request",
							text: "IB Application Request sent successfully! Kindly wait for admin approval.",
							icon: "success"
						}).then(() => {
							location.reload();
						});
					}
				},
				error: function () {
					Swal.close();

					Swal.fire({
						title: "IB Request",
						text: "IB Application Request was not submitted. Try again!",
						icon: "error"
					}).then(() => {
						location.reload();
					});
				}
			});
		});
	});
  
  
    /*$(".ib-enroll").click(function() {
      $.ajax({
        url: "{{ route('ib-enroll') }}",
        data: "ib_enroll=true",
        type: "POST",
        beforeSend: function() {
          Swal.fire({
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function() {
              Swal.enableLoading();
            }
          });
        },
        success: function(data) {
          Swal.close();
          if (data.status == 'true') {
            Swal.fire({
              title: "You're Successfully enrolled as an IB",
              text: "Share and Earn",
              icon: "success"
            }).then((val) => {
              location.reload();
            });
          }
        }
      });
    });*/
  </script>

@endsection
