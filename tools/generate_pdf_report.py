#!/usr/bin/env python3
"""
Script para generar el Informe Técnico en PDF para la evidencia GA8-220501096-AA2-EV02
"""

import os
import sys

def generate_html_report(md_path, html_path):
    with open(md_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Convert simple markdown to styled HTML
    import re

    # Escaping and structuring
    html_lines = []
    lines = content.split('\n')
    in_table = False
    table_rows = []
    in_code = False
    code_block = []
    code_lang = ""

    for line in lines:
        if line.startswith('```'):
            if in_code:
                in_code = False
                code_text = '\n'.join(code_block)
                html_lines.append(f'<pre><code class="language-{code_lang}">{escape_html(code_text)}</code></pre>')
                code_block = []
                code_lang = ""
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

        if line.startswith('# '):
            html_lines.append(f'<h1>{format_inline(line[2:])}</h1>')
        elif line.startswith('## '):
            html_lines.append(f'<h2>{format_inline(line[3:])}</h2>')
        elif line.startswith('### '):
            html_lines.append(f'<h3>{format_inline(line[4:])}</h3>')
        elif line.startswith('#### '):
            html_lines.append(f'<h4>{format_inline(line[5:])}</h4>')
        elif line.startswith('- ') or line.startswith('* '):
            html_lines.append(f'<li>{format_inline(line[2:])}</li>')
        elif line.strip() == '---':
            html_lines.append('<hr/>')
        elif line.strip() == '':
            html_lines.append('<p></p>')
        else:
            html_lines.append(f'<p>{format_inline(line)}</p>')

    if in_table:
        html_lines.append(render_table(table_rows))

    body_html = '\n'.join(html_lines)

    full_html = f"""<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>GA8-220501096-AA2-EV02 - Informe Técnico</title>
<style>
    @page {{
        size: A4;
        margin: 20mm 15mm 20mm 15mm;
        @bottom-right {{
            content: counter(page);
        }}
    }}
    body {{
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        color: #1e293b;
        line-height: 1.6;
        font-size: 11pt;
        background: #ffffff;
        margin: 0;
        padding: 20px;
    }}
    h1 {{
        color: #0f766e;
        font-size: 20pt;
        border-bottom: 2px solid #0f766e;
        padding-bottom: 6px;
        margin-top: 24px;
        margin-bottom: 12px;
    }}
    h2 {{
        color: #0369a1;
        font-size: 15pt;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 4px;
        margin-top: 20px;
        margin-bottom: 10px;
        page-break-after: avoid;
    }}
    h3 {{
        color: #334155;
        font-size: 12pt;
        margin-top: 16px;
        margin-bottom: 8px;
        page-break-after: avoid;
    }}
    p {{
        margin-bottom: 8px;
        text-align: justify;
    }}
    hr {{
        border: 0;
        border-top: 1px solid #e2e8f0;
        margin: 20px 0;
    }}
    table {{
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0;
        font-size: 10pt;
        page-break-inside: avoid;
    }}
    th, td {{
        border: 1px solid #cbd5e1;
        padding: 8px 10px;
        text-align: left;
    }}
    th {{
        background-color: #f1f5f9;
        color: #0f172a;
        font-weight: 600;
    }}
    tr:nth-child(even) {{
        background-color: #f8fafc;
    }}
    pre {{
        background: #0f172a;
        color: #f8fafc;
        padding: 12px;
        border-radius: 6px;
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 9.5pt;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        page-break-inside: avoid;
        margin: 12px 0;
    }}
    code {{
        font-family: 'Consolas', 'Courier New', monospace;
        background: #e2e8f0;
        color: #0f172a;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 9.5pt;
    }}
    pre code {{
        background: transparent;
        color: inherit;
        padding: 0;
    }}
    li {{
        margin-bottom: 4px;
    }}
    .badge-success {{
        background: #dcfce7;
        color: #166534;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
    }}
</style>
</head>
<body>
{body_html}
</body>
</html>"""

    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(full_html)
    print(f"HTML generado exitosamente en: {html_path}")

def escape_html(text):
    return text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')

def format_inline(text):
    import re
    # Bold **text**
    text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', text)
    # Inline code `code`
    text = re.sub(r'`([^`]+)`', r'<code>\1</code>', text)
    # Links [text](url)
    text = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', text)
    return text

def render_table(rows):
    if len(rows) < 2:
        return ""
    header_cols = [c.strip() for c in rows[0].split('|')[1:-1]]
    
    # Check if row 1 is separator
    start_row = 1
    if len(rows) > 1 and '---' in rows[1]:
        start_row = 2

    html = ['<table>', '<thead>', '<tr>']
    for h in header_cols:
        html.append(f'<th>{format_inline(h)}</th>')
    html.extend(['</tr>', '</thead>', '<tbody>'])

    for r in rows[start_row:]:
        cols = [c.strip() for c in r.split('|')[1:-1]]
        html.append('<tr>')
        for c in cols:
            html.append(f'<td>{format_inline(c)}</td>')
        html.append('</tr>')

    html.extend(['</tbody>', '</table>'])
    return '\n'.join(html)

if __name__ == '__main__':
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    md_file = os.path.join(base_dir, 'Documentacion', 'GA8_AA2_EV02_Informe.md')
    html_file = os.path.join(base_dir, 'Documentacion', 'GA8_AA2_EV02_Informe.html')
    generate_html_report(md_file, html_file)
