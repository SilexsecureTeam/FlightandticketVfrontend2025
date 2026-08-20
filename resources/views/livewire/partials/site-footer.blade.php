<footer class="bg-[#262626] pt-10 pb-7 text-white">
    <section class="mb-10 px-5 md:px-8 lg:px-24 flex flex-col md:flex-row gap-7 justify-between">
        <a href="/">
            <img src="/images/logo.png" alt="Flightandticketing logo" class="max-w-[156px] h-[74px]" />
        </a>
        <div class="flex flex-wrap justify-between gap-10 ld:gap-20 text-xs">
            <div class="flex flex-col gap-3">
                <h3 class="text-base">Company</h3>
                @forelse ($companyPages as $page)
                    <a href="{{ $page->url }}" class="hover:underline">{{ $page->title }}</a>
                @empty
                    <span class="text-white/60">—</span>
                @endforelse
            </div>
            <div class="flex flex-col gap-3">
                <h3 class="text-base">Legal</h3>
                @forelse ($legalPages as $page)
                    <a href="{{ $page->url }}" class="hover:underline">{{ $page->title }}</a>
                @empty
                    <span class="text-white/60">—</span>
                @endforelse
            </div>
            @if ($otherPages->isNotEmpty())
                <div class="flex flex-col gap-3">
                    <h3 class="text-base">More</h3>
                    @foreach ($otherPages as $page)
                        <a href="{{ $page->url }}" class="hover:underline">{{ $page->title }}</a>
                    @endforeach
                    <a href="{{ route('blog.index') }}" class="hover:underline">Blog</a>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    <h3 class="text-base">Resources</h3>
                    <a href="{{ route('blog.index') }}" class="hover:underline">Blog</a>
                </div>
            @endif
            <div class="flex flex-col gap-3">
                <h3 class="text-base">Currency</h3>
                <div class="relative flex items-center gap-2 border border-white px-2 py-1 w-fit min-w-[140px] pr-6">
                    <select class="bg-transparent text-white outline-none appearance-none w-full">
                        <option class="text-black" value="naira" selected>🇳🇬&emsp;Naira</option>
                        <option class="text-black" value="usd">🇺🇸&emsp;USD</option>
                        <option class="text-black" value="euro">🇪🇺&emsp;Euro</option>
                    </select>
                    <span class="absolute right-1 top-1/2 transform -translate-y-1/2">
                        <img src="/images/toggle.svg" alt="Dropdown" class="w-2 h-1.5" />
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="mb-5 pl-5 md:pl-8 lg:pl-20">
            <p class="mb-3">Connect with Us</p>
            <div class="flex gap-4">
                <img src="/images/facebook.svg" alt="facebook icon">
                <img src="/images/instagram.svg" alt="instagram icon">
                <img src="/images/linkedin.svg" alt="linkedin icon">
            </div>
        </div>

        <div class="glow-line mb-10"></div>

        <div class="flex items-center justify-center text-center gap-1">
            <img src="/images/copyright.svg" alt="copyright icon" class="w-5 h-5">
            <p class="text-sm">{{ date('Y') }} Mymore Inc.</p>
        </div>
    </section>
</footer>
