# SecurePDF Moodle Module

This plugin provides secure PDF download functionality for Moodle.  
It allows course administrators and teachers to offer PDF files that are:

- Watermarked with the user's email
- Centered and rotated diagonally
- Merged into the PDF to make removal harder
- Non-clickable and non-selectable text

---

## Plugin Details

- **Package:** `mod_securepdf`  
- **Copyright:** 2026 Nephila Web Technology Inc.  
- **Author:** Roy Plomantes  
- **License:** [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html)  

---

## Requirements

- Moodle 4.x or later
- PHP 7.4+  
- GD library enabled for image watermarking

---

## Installation

1. Copy the `securepdf` folder to `moodle/mod/`  
2. Visit Site administration → Notifications to complete installation  
3. Configure module settings under each course

---

## Features

- Secure watermarking of PDFs  
- Flattened image-based watermark (harder to remove)  
- Option to download or view inline  
- Permissions-based access (teachers vs. students)  
- Configurable opacity, rotation, and font multiplier  

---

## License

This plugin is free software: you can redistribute it and/or modify  
it under the terms of the GNU General Public License as published by  
the Free Software Foundation, either version 3 of the License, or  
(at your option) any later version.

Moodle is distributed in the hope that it will be useful,  
but WITHOUT ANY WARRANTY; without even the implied warranty of  
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the  
GNU General Public License for more details.

You should have received a copy of the GNU General Public License  
along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

