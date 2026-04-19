# PWA Icons Setup Guide

## Overview
This guide explains how to set up icons for your OneTera PWA installation.

## Icon Requirements

### Icon Files Needed
1. **icon-192x192.png** - Main app icon (192×192 pixels)
2. **icon-512x512.png** - Large app icon (512×512 pixels)
3. **maskable-icon-192x192.png** - Maskable icon for adaptive icons (192×192 pixels)
4. **maskable-icon-512x512.png** - Maskable icon for adaptive icons (512×512 pixels)
5. **badge-72x72.png** - Badge icon for notifications (72×72 pixels)

### Icon Specifications

#### Standard Icons (icon-192x192.png, icon-512x512.png)
- **Size**: 192×192 or 512×512 pixels
- **Format**: PNG with transparency
- **Colors**: Full color recommended
- **Safe Area**: Place content within center (avoid edges)
- **Purpose**: App launcher icon on home screen

#### Maskable Icons (maskable-icon-192x192.png, maskable-icon-512x512.png)
- **Size**: 192×192 or 512×512 pixels
- **Format**: PNG with transparency
- **Safe Area**: Critical content within center circle (minimum 40% of size)
- **Background**: Transparent or with color
- **Purpose**: Adaptive icons on modern Android devices
- **Note**: The system will mask this icon into various shapes

#### Badge Icon (badge-72x72.png)
- **Size**: 72×72 pixels
- **Format**: PNG with transparency
- **Color**: Monochrome preferred (white or light color on transparent)
- **Purpose**: Shown when notification badge is active

## How to Create Icons

### Option 1: Using Online Tools
1. Visit https://www.favicon-generator.org/ or https://realfavicongenerator.net/
2. Upload your logo or design
3. Generate all required sizes
4. Download and extract to `/public/assets/icons/`

### Option 2: Using Figma
1. Create a 512×512 design
2. Export as PNG @2x (produces 1024×1024)
3. Use image resizing tool to scale down
4. For maskable icons, ensure safe area

### Option 3: Using Command Line (ImageMagick)
```bash
# Create 192×192 icon from source
convert source.png -resize 192x192 icon-192x192.png

# Create 512×512 icon from source
convert source.png -resize 512x512 icon-512x512.png

# Create maskable variant
convert icon-192x192.png -background transparent -gravity center \
  -extent 192x192 maskable-icon-192x192.png
```

### Option 4: Using Online Converters
- **CloudConvert**: https://cloudconvert.com/
- **Ezgif**: https://ezgif.com/
- **TinyPNG**: https://tinypng.com/

## Implementation Checklist

- [ ] Create 192×192 PNG icon
- [ ] Create 512×512 PNG icon
- [ ] Create 192×192 maskable icon variant
- [ ] Create 512×512 maskable icon variant
- [ ] Create 72×72 badge icon
- [ ] Place all files in `/public/assets/icons/`
- [ ] Test manifest.json validity
- [ ] Test on Android device/emulator
- [ ] Test on iOS device
- [ ] Verify icons appear in home screen

## File Locations

```
public/assets/icons/
├── icon-192x192.png          # Standard icon (192×192)
├── icon-512x512.png          # Standard icon (512×512)
├── icon-192x192.svg          # Optional SVG variant
├── icon-512x512.svg          # Optional SVG variant
├── maskable-icon-192x192.png # Maskable variant (192×192)
├── maskable-icon-512x512.png # Maskable variant (512×512)
└── badge-72x72.png           # Notification badge (72×72)
```

## Design Guidelines

### Color
- **Primary Color**: #3b82f6 (Blue)
- **Secondary Color**: #764ba2 (Purple)
- **Gradient**: Use gradient for visual appeal

### Design Elements
- Keep design simple and recognizable at small sizes
- Use app branding (logo, initials, etc.)
- Avoid text except initials
- Ensure good contrast between icon and background

### Safe Areas for Maskable Icons
- **Minimum**: Center 40% of the image
- **Recommended**: Center 60% of the image
- **Example for 192×192**: Safe area is center 115×115 pixels
- **Example for 512×512**: Safe area is center 307×307 pixels

## Testing Your Icons

### On Android
1. Install PWA from Chrome
2. Long-press app to see icon
3. Verify it's not distorted or cut off
4. Check adaptive icon shape on Android 8+

### On iOS
1. Add app to home screen from Safari
2. Verify icon appears correctly
3. Check both light and dark mode appearance

### Using DevTools
1. Open DevTools (F12)
2. Go to Application → Manifest
3. Verify all icons are listed
4. Check for any errors or warnings

## Manifest Configuration

The manifest.json automatically references your icons:

```json
"icons": [
    {
      "src": "/assets/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/assets/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/assets/icons/maskable-icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "maskable"
    },
    {
      "src": "/assets/icons/maskable-icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "maskable"
    }
]
```

## Troubleshooting

### Icons not appearing on home screen
- Check file exists at correct path
- Verify file is PNG format
- Ensure file is not corrupted
- Clear cache and reinstall PWA

### Icon appears distorted
- Check image size matches specification
- For maskable icons, ensure safe area is clear
- Avoid text in icon (except initials)
- Use vector graphics that scale well

### App doesn't install
- Verify manifest.json is valid
- Check all icon files exist
- Use 192×192 and 512×512 minimum
- Ensure HTTPS is enabled

## Best Practices

1. **Use SVG**: When possible, create SVG icons for best scaling
2. **Test Sizes**: Always test at actual device sizes
3. **Simple Design**: Simpler designs work better at small sizes
4. **Brand Consistency**: Match your existing brand guidelines
5. **Accessibility**: Ensure sufficient contrast ratio (4.5:1)
6. **Performance**: Optimize PNG files for web (compress)

## Resources

- [MDN: Web App Icons](https://developer.mozilla.org/en-US/docs/Mozilla/Add-ons/WebExtensions/manifest.json/icons)
- [PWA Icons Specification](https://www.w3.org/TR/appmanifest/#icons-member)
- [Android Adaptive Icons](https://developer.android.com/guide/practices/ui_guidelines/icon_design_adaptive)
- [iOS App Icon Design](https://developer.apple.com/design/human-interface-guidelines/app-icons)

## Quick Start

1. Download template from Figma or Canva
2. Design your icon (simple, bold, recognizable)
3. Export as PNG at 512×512
4. Use image converter to create all variants
5. Upload to `/public/assets/icons/`
6. Test on real devices
7. Optimize PNGs with TinyPNG

---

For questions or support, refer to the PWA_IMPLEMENTATION.md documentation.
