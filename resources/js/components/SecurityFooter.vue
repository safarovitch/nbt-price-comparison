<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
</script>

<template>
    <div class="security-footer border border-t">
        <div class="container">
            <div class="security-header mb-4">
                <h2>{{ __('We care about your safety') }}</h2>
                <p>{{ __('For letters and requests from state bodies') }} <a href="mailto:legal@nbt.tj">legal@nbt.tj</a></p>
            </div>

            <div class="row g-4">
                <!-- Column 1: Data Protection -->
                <div class="col-lg-4">
                    <div class="security-card bg-light-gray">
                        <h3 style="width:220px">{{ __('Your personal data is securely protected') }}</h3>
                        <ul class="security-list">
                            <li>
                                <i class="fa-regular fa-square-check text-success"></i>
                                <span>{{ __('We undergo regular information security audits') }}</span>
                            </li>
                            <li>
                                <i class="fa-regular fa-square-check text-success"></i>
                                <span>{{ __('All data is transmitted in encrypted form') }}</span>
                            </li>
                            <li>
                                <i class="fa-regular fa-square-check text-success"></i>
                                <span>{{ __('Included in the register of personal data operators of Roskomnadzor') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Column 2: Security Rules -->
                <div class="col-lg-4">
                    <div class="security-card bg-light-gray">
                        <div class="d-flex align-items-start gap-3">
                            <img src="/storage/images/icons/shield-icon.webp" alt="Security" width="32">
                            <h3 style="width:220px">{{ __('Important security rules') }}</h3>
                        </div>
                        <ul class="security-list mt-1">
                            <li>
                                <i class="fa-solid fa-phone text-secondary"></i>
                                <span>{{ __('Do not tell anyone your personal data or SMS code') }}</span>
                            </li>
                            <li>
                                <i class="fa-regular fa-message text-secondary"></i>
                                <span>{{ __('Employees of Sravni and its representatives do not request SMS under any circumstances') }}</span>
                            </li>
                            <li>
                                <i class="fa-regular fa-square-check text-secondary"></i>
                                <span>{{ __('If in doubt, call the official number on the website') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Column 3: Customer Care -->
                <div class="col-lg-4">
                    <div class="security-card bg-light-gray">
                        <h3>{{ __('Customer Care Service') }}</h3>
                        <p class="small text-muted mb-4">
                            {{ __('If you were contacted on behalf of Sravni with a request to provide personal data, contact support in the chat in the application, or send a message to') }} <a href="mailto:help@nbt.tj">help@nbt.tj</a>
                        </p>
                        <div class="support-hours">
                            {{ __('Daily from 8:00 to 18:00 Dushanbe time') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>