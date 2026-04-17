<?php

namespace App\Http\Requests;

/**
 * This file is deprecated. All FormRequest classes have been moved to separate files:
 * 
 * - LoginRequest.php
 * - RegisterRequest.php
 * - PasswordResetEmailRequest.php
 * - PasswordUpdateRequest.php
 * - ContactFormRequest.php
 * - TurnstileValidationMixin.php (trait)
 * 
 * This file is kept for backward compatibility. Please use the individual files instead.
 */

require_once __DIR__ . '/TurnstileValidationMixin.php';
require_once __DIR__ . '/LoginRequest.php';
require_once __DIR__ . '/RegisterRequest.php';
require_once __DIR__ . '/PasswordResetEmailRequest.php';
require_once __DIR__ . '/PasswordUpdateRequest.php';
require_once __DIR__ . '/ContactFormRequest.php';
