#!/usr/bin/env python3
"""
Script de compilación para la evidencia SENA:
GA9-220501096-AA1-EV02 - Plan de Pruebas de Software
"""

import os
import shutil
import zipfile
import subprocess

def escape_html(text):
    return text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')

def format_inline(text):
    import re
    text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', text)
    text = re.sub(r'`([^`]+)`', r'<code>\1</code>', text)
    text = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', text)
    return text

def render_table(rows):
    if len(rows) < 2:
        return ''
    header = [c.strip() for c in rows[0].split('|')[1:-1]]
    start = 1
    if len(rows) > 1 and '---' in rows[1]:
        start = 2
    html = ['<table>', '<thead>', '<tr>']
    for h in header:
        html.append(f'<th>{format_inline(h)}</th>')
    html.extend(['</tr>', '</thead>', '<tbody>'])
    for r in rows[start:]:
        cols = [c.strip() for c in r.split('|')[1:-1]]
        html.append('<tr>')
        for c in cols:
            html.append(f'<td>{format_inline(c)}</td>')
        html.append('</tr>')
    html.extend(['</tbody>', '</table>'])
    return '\n'.join(html)

def generate_html(md_path, html_path):
    with open(md_path, 'r', encoding='utf-8') as f:
        content = f.read()
    lines = content.split('\n')
    html_lines = []
    in_code = False
    code_block = []
    code_lang = ''
    in_table = False
    table_rows = []
    for line in lines:
        if line.startswith('```'):
            if in_code:
                in_code = False
                code_text = '\n'.join(code_block)
                html_lines.append(f'<pre><code class="language-{code_lang}">{escape_html(code_text)}</code></pre>')
                code_block = []
                code_lang = ''
            else:
                in_code = True
                code_lang = line[3:].strip()
            continue
        if in_code:
            code_block.append(line)
            continue
        if line.strip().startswith('|') and line.strip().endswith('|'):
            if not in_table:
                in_table = True
                table_rows = []
            table_rows.append(line.strip())
            continue
        else:
            if in_table:
                in_table = False
                html_lines.append(render_table(table_rows))
                table_rows = []
        img_match = __import__('re').match(r'!\[(.*?)\]\((.*?)\)', line.strip())
        if img_match:
            alt, src = img_match.groups()
            html_lines.append(f'<div class="img-container"><img src="{src}" alt="{alt}" /></div>')
            continue
        if line.strip():
            html_lines.append(f'<p>{format_inline(line.strip())}</p>')
    if in_table:
        html_lines.append(render_table(table_rows))
    body_html = '\n'.join(html_lines)
    full_html = f"""<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<title>Plan de Pruebas de Software – GA9‑AA1‑EV02</title>
<style>
body {{font-family: 'Inter', sans-serif; padding: 2rem; line-height: 1.6; background:#fafafa;}}
h1 {{color:#047857; font-size:1.8rem; border-bottom:2px solid #047857; padding-bottom:0.4rem;}}
h2 {{color:#0284c7; font-size:1.5rem; border-bottom:1px solid #cbd5e1; padding-bottom:0.3rem;}}
table {{width:100%; border-collapse:collapse; margin:1rem 0;}}
th, td {{border:1px solid #cbd5e1; padding:0.5rem; text-align:left;}}
th {{background:#f1f5f9;}}
pre {{background:#0f172a; color:#f8fafc; padding:1rem; overflow:auto; border-radius:4px;}}
code {{background:#e2e8f0; color:#0f172a; padding:0.2rem 0.4rem; border-radius:3px;}}
.img-container {{text-align:center; margin:1rem 0;}}
.img-container img {{max-width:90%; border:1px solid #cbd5e1; border-radius:4px;}}
</style>
</head>
<body>
{body_html}
</body>
</html>"""
    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(full_html)
    print(f"HTML generado exitosamente en: {html_path}")

def main():
    base_dir = os.path.abspath(os.path.join(__file__, '..', '..'))
    doc_dir = os.path.join(base_dir, 'Documentacion')
    md_file = os.path.join(doc_dir, 'GA9_AA1_EV02_Plan_De_Pruebas.md')
    html_file = os.path.join(doc_dir, 'GA9_AA1_EV02_Plan_De_Pruebas.html')
    pdf_file = os.path.join(doc_dir, 'GA9_AA1_EV02_Plan_De_Pruebas.pdf')

    print("--> 1. Generando archivo HTML estilizado...")
    generate_html(md_file, html_file)

    print("--> 2. Compilando PDF con Chrome Headless...")
    chrome_bin = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
    cmd = [chrome_bin, '--headless', '--disable-gpu', f'--print-to-pdf={pdf_file}', html_file]
    res = subprocess.run(cmd, capture_output=True)
    if os.path.exists(pdf_file) and os.path.getsize(pdf_file) > 0:
        print(f"✅ PDF generado exitosamente ({os.path.getsize(pdf_file)} bytes): {pdf_file}")
    else:
        print(f"❌ Error al generar PDF: {res.stderr.decode()}")
        return

    print("--> 3. Preparando carpeta del entregable...")
    deliverable_dir = os.path.join(base_dir, 'JOSE_LUIS_HERNANDEZ_GA9_AA1_EV02')
    if os.path.exists(deliverable_dir):
        shutil.rmtree(deliverable_dir)
    os.makedirs(deliverable_dir, exist_ok=True)

    # copiar artefactos principales
    shutil.copy2(pdf_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV02_Plan_De_Pruebas.pdf'))
    shutil.copy2(md_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV02_Plan_De_Pruebas.md'))
    shutil.copy2(html_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV02_Plan_De_Pruebas.html'))

    # copiar assets y pruebas
    shutil.copytree(os.path.join(doc_dir, 'assets'), os.path.join(deliverable_dir, 'assets'))
    shutil.copytree(os.path.join(base_dir, 'tests'), os.path.join(deliverable_dir, 'tests'))
    shutil.copy2(os.path.join(base_dir, 'phpunit.xml'), deliverable_dir)
    shutil.copy2(os.path.join(base_dir, 'KenkoPOS API - Productos.postman_collection.json'), deliverable_dir)
    shutil.copy2(os.path.join(base_dir, 'KenkoPOS.postman_collection.json'), deliverable_dir)

    # código fuente esencial
    for folder in ['api', 'app', 'config', 'database', 'public']:
        src = os.path.join(base_dir, folder)
        dst = os.path.join(deliverable_dir, folder)
        if os.path.exists(src):
            shutil.copytree(src, dst, ignore=shutil.ignore_patterns('__pycache__', '.DS_Store', '*.pyc'))

    # archivo de referencia
    repo_txt = """SERVICIO NACIONAL DE APRENDIZAJE – SENA\nPrograma: Tecnólogo en Análisis y Desarrollo de Software (ADSO)\nEvidencia: GA9-220501096-AA1-EV02 - Plan de Pruebas de Software\nAprendiz: Jose Luis Hernandez\nInstructora: Luz Karime Castellanos\nFecha: Septiembre 6 de 2026\n\nRepositorio Oficial en GitHub:\nhttps://github.com/Dejosel/kenkopos\n\nRama Principal:\nmain\n\nVersión del Sistema:\nv1.5.0 (PHP Web Stack + Testing Suite)\n\nContenido de la Entrega:\n1. GA9-220501096-AA1-EV02_Plan_De_Pruebas.pdf\n2. GA9-220501096-AA1-EV02_Plan_De_Pruebas.md\n3. GA9-220501096-AA1-EV02_Plan_De_Pruebas.html\n4. tests/ (suites de pruebas)\n5. phpunit.xml\n6. collection Postman y assets\n7. Código fuente (api, app, config, database, public)\n"""
    with open(os.path.join(deliverable_dir, 'Repositorio_y_Instrucciones.txt'), 'w', encoding='utf-8') as f:
        f.write(repo_txt)

    print("--> 4. Creando archivo ZIP comprimido...")
    zip_filename = os.path.join(base_dir, 'JOSE_LUIS_HERNANDEZ_GA9_AA1_EV02.zip')
    with zipfile.ZipFile(zip_filename, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(deliverable_dir):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, base_dir)
                zipf.write(file_path, arcname)
    print(f"✅ Paquete ZIP generado exitosamente ({os.path.getsize(zip_filename)} bytes): {zip_filename}")
    print("="*60)
    print("🎉 ¡TODOS LOS ENTREGABLES FUERON GENERADOS CORRECTAMENTE!")
    print("="*60)

if __name__ == '__main__':
    main()
