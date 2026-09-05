#!/usr/bin/env python3
"""
Script de compilación y empaquetado del entregable SENA:
GA8-220501096-AA2-EV02
"""

import os
import shutil
import zipfile
import subprocess
from generate_pdf_report import generate_html_report

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    doc_dir = os.path.join(base_dir, 'Documentacion')
    md_file = os.path.join(doc_dir, 'GA8_AA2_EV02_Informe.md')
    html_file = os.path.join(doc_dir, 'GA8_AA2_EV02_Informe.html')
    pdf_file = os.path.join(doc_dir, 'GA8_AA2_EV02_Informe.pdf')

    # 1. Generar HTML
    print("--> 1. Generando archivo HTML estilizado...")
    generate_html_report(md_file, html_file)

    # 2. Intentar compilar a PDF con herramientas disponibles
    print("--> 2. Intentando generar PDF...")
    pdf_generated = False
    
    # Intentar con weasyprint
    try:
        from weasyprint import HTML
        HTML(html_file).write_pdf(pdf_file)
        pdf_generated = True
        print(f"PDF generado exitosamente con WeasyPrint en: {pdf_file}")
    except Exception as e:
        print(f"WeasyPrint no disponible ({e}), intentando métodos alternativos...")

    # Intentar con wkhtmltopdf
    if not pdf_generated:
        try:
            res = subprocess.run(["wkhtmltopdf", html_file, pdf_file], capture_output=True)
            if res.returncode == 0:
                pdf_generated = True
                print(f"PDF generado exitosamente con wkhtmltopdf en: {pdf_file}")
        except Exception:
            pass

    # Intentar con Chrome / Chromium headless
    if not pdf_generated:
        for chrome_bin in ["/Applications/Google Chrome.app/Contents/MacOS/Google Chrome", "google-chrome", "chromium"]:
            if os.path.exists(chrome_bin) or shutil.which(chrome_bin):
                try:
                    cmd = [
                        chrome_bin,
                        "--headless",
                        "--disable-gpu",
                        f"--print-to-pdf={pdf_file}",
                        html_file
                    ]
                    res = subprocess.run(cmd, capture_output=True)
                    if os.path.exists(pdf_file) and os.path.getsize(pdf_file) > 0:
                        pdf_generated = True
                        print(f"PDF generado exitosamente con Chrome Headless en: {pdf_file}")
                        break
                except Exception as e:
                    print(f"Fallo Chrome headless: {e}")

    # Fallback: ReportLab si está disponible
    if not pdf_generated:
        try:
            from reportlab.lib.pagesizes import letter
            from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
            from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
            from reportlab.lib import colors

            doc = SimpleDocTemplate(pdf_file, pagesize=letter, rightMargin=40, leftMargin=40, topMargin=40, bottomMargin=40)
            styles = getSampleStyleSheet()
            story = []

            # Custom styles
            title_style = ParagraphStyle(
                'DocTitle',
                parent=styles['Heading1'],
                fontSize=18,
                textColor=colors.HexColor('#0f766e'),
                spaceAfter=12
            )
            h2_style = ParagraphStyle(
                'DocH2',
                parent=styles['Heading2'],
                fontSize=13,
                textColor=colors.HexColor('#0369a1'),
                spaceBefore=14,
                spaceAfter=6
            )
            body_style = ParagraphStyle(
                'DocBody',
                parent=styles['Normal'],
                fontSize=9.5,
                leading=13,
                textColor=colors.HexColor('#1e293b'),
                spaceAfter=6
            )

            with open(md_file, 'r', encoding='utf-8') as f:
                md_content = f.read()

            for line in md_content.split('\n'):
                line_str = line.strip()
                if not line_str:
                    continue
                if line_str.startswith('# '):
                    story.append(Paragraph(line_str[2:], title_style))
                elif line_str.startswith('## '):
                    story.append(Paragraph(line_str[3:], h2_style))
                elif line_str.startswith('### '):
                    story.append(Paragraph(line_str[4:], h2_style))
                elif line_str.startswith('- ') or line_str.startswith('* '):
                    story.append(Paragraph(f"• {line_str[2:]}", body_style))
                elif line_str.startswith('|') and not '---' in line_str:
                    cols = [c.strip() for c in line_str.split('|')[1:-1]]
                    story.append(Paragraph(" | ".join(cols), body_style))
                elif line_str.startswith('```'):
                    continue
                else:
                    story.append(Paragraph(line_str, body_style))

            doc.build(story)
            pdf_generated = True
            print(f"PDF generado exitosamente con ReportLab en: {pdf_file}")
        except Exception as e:
            print(f"ReportLab fallback: {e}")

    # 3. Crear Carpeta de Entrega y Empaquetar ZIP
    print("--> 3. Creando carpeta de entrega y empaquetando ZIP...")
    deliverable_folder_name = "JOSE_LUIS_HERNANDEZ_GA8_AA2_EV02"
    deliverable_dir = os.path.join(base_dir, deliverable_folder_name)
    
    if os.path.exists(deliverable_dir):
        shutil.rmtree(deliverable_dir)
    os.makedirs(deliverable_dir, exist_ok=True)

    # Copiar documentos clave
    if os.path.exists(pdf_file):
        shutil.copy2(pdf_file, os.path.join(deliverable_dir, "GA8-220501096-AA2-EV02_Informe_Tecnico.pdf"))
    if os.path.exists(html_file):
        shutil.copy2(html_file, os.path.join(deliverable_dir, "GA8-220501096-AA2-EV02_Informe_Tecnico.html"))
    shutil.copy2(md_file, os.path.join(deliverable_dir, "GA8-220501096-AA2-EV02_Informe_Tecnico.md"))
    
    repo_src = os.path.join(doc_dir, 'GA8_AA2_EV02_Repositorio.txt')
    if os.path.exists(repo_src):
        shutil.copy2(repo_src, os.path.join(deliverable_dir, "Repositorio_y_Instrucciones.txt"))

    # Copiar carpetas del código fuente
    for folder in ['api', 'app', 'config', 'database', 'public', 'frontend', 'Documentacion']:
        src_path = os.path.join(base_dir, folder)
        dst_path = os.path.join(deliverable_dir, folder)
        if os.path.exists(src_path):
            shutil.copytree(src_path, dst_path, ignore=shutil.ignore_patterns('node_modules', '.git', '*.sqlite-journal', '__pycache__'))

    for single_file in ['index.php', 'README.md', 'ENDPOINTS.md']:
        src_f = os.path.join(base_dir, single_file)
        if os.path.exists(src_f):
            shutil.copy2(src_f, os.path.join(deliverable_dir, single_file))

    # Crear ZIP
    zip_path = os.path.join(base_dir, f"{deliverable_folder_name}.zip")
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(deliverable_dir):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, base_dir)
                zipf.write(file_path, arcname)

    print(f"Paquete ZIP generado exitosamente en: {zip_path}")
    print(f"Tamaño del ZIP: {os.path.getsize(zip_path) / (1024*1024):.2f} MB")

if __name__ == '__main__':
    main()
