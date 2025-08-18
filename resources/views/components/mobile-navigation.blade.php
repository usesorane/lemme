<div x-on:keydown.escape="mobileNavOpen = false"
     x-on:click.away="mobileNavOpen = false"
     class="fixed inset-0 top-14 z-50 lg:hidden" role="dialog"
     tabindex="-1"
     aria-modal="true"
     x-bind:data-open="mobileNavOpen"
     x-bind:data-closed="!mobileNavOpen">

    <!-- backdrop -->
    <div class="fixed inset-0  top-14 bg-zinc-400/20 backdrop-blur-xs data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-black/40"
         aria-hidden="true" data-open=""></div>

    <!-- panel -->
    <div data-open="">
        <div class="fixed top-14 bottom-0 left-0 w-full overflow-y-auto bg-white px-4 pt-6 pb-4 shadow-lg ring-1 shadow-zinc-900/10 ring-zinc-900/7.5 duration-500 ease-in-out data-closed:-translate-x-full min-[416px]:max-w-sm sm:px-6 sm:pb-10 dark:bg-zinc-900 dark:ring-zinc-800"
             style="">
            <nav>
                <ul role="list">
                    <li class="md:hidden">
                        <a class="block py-1 text-sm text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                           type="button" data-headlessui-state="" href="/">API</a></li>
                    <li class="md:hidden"><a
                                class="block py-1 text-sm text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                type="button" data-headlessui-state="" href="#">Documentation</a></li>
                    <li class="md:hidden"><a
                                class="block py-1 text-sm text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                type="button" data-headlessui-state="" href="#">Support</a></li>
                    <li class="relative mt-6 md:mt-0"><h2 class="text-xs font-semibold text-zinc-900 dark:text-white">
                            Guides</h2>
                        <div class="relative mt-3 pl-2">
                            <div class="absolute inset-x-0 top-0 bg-zinc-800/2.5 will-change-transform dark:bg-white/2.5"
                                 style="height: 32px; top: 0px; border-radius: 8px; opacity: 1;"></div>
                            <div class="absolute inset-y-0 left-2 w-px bg-zinc-900/10 dark:bg-white/5"></div>
                            <div class="absolute left-2 h-6 w-px bg-emerald-500" style="top: 4px; opacity: 1;"></div>
                            <ul role="list" class="border-l border-transparent">
                                <li class="relative"><a aria-current="page"
                                                        class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-900 dark:text-white"
                                                        type="button" data-headlessui-state="" href="/"><span
                                                class="truncate">Introduction</span></a>
                                    <ul role="list" style="opacity: 1;">
                                        <li>
                                            <a class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-7 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                               type="button" data-headlessui-state="" href="/#guides"><span
                                                        class="truncate">Guides</span></a></li>
                                        <li>
                                            <a class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-7 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                               type="button" data-headlessui-state="" href="/#resources"><span
                                                        class="truncate">Resources</span></a></li>
                                    </ul>
                                </li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/quickstart"><span
                                                class="truncate">Quickstart</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/sdks"><span class="truncate">SDKs</span></a>
                                </li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/authentication"><span
                                                class="truncate">Authentication</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/pagination"><span
                                                class="truncate">Pagination</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/errors"><span
                                                class="truncate">Errors</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/webhooks"><span
                                                class="truncate">Webhooks</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="relative mt-6"><h2 class="text-xs font-semibold text-zinc-900 dark:text-white">
                            Resources</h2>
                        <div class="relative mt-3 pl-2">
                            <div class="absolute inset-y-0 left-2 w-px bg-zinc-900/10 dark:bg-white/5"></div>
                            <ul role="list" class="border-l border-transparent">
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/contacts"><span
                                                class="truncate">Contacts</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/conversations"><span
                                                class="truncate">Conversations</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/messages"><span
                                                class="truncate">Messages</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/groups"><span
                                                class="truncate">Groups</span></a></li>
                                <li class="relative"><a
                                            class="flex justify-between gap-2 py-1 pr-3 text-sm transition pl-4 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                            type="button" data-headlessui-state="" href="/attachments"><span
                                                class="truncate">Attachments</span></a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="sticky bottom-0 z-10 mt-6 min-[416px]:hidden"><a
                                class="inline-flex gap-0.5 justify-center overflow-hidden text-sm font-medium transition rounded-full bg-zinc-900 py-1 px-3 text-white hover:bg-zinc-700 dark:bg-emerald-500 dark:text-white dark:hover:bg-emerald-400 w-full"
                                href="#">Sign in</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
