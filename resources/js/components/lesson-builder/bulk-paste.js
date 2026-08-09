/**
 * Parses pasted delimited text into rows of plain string columns — one line
 * per item, columns separated by `|` or a tab. Purely a tokenizer; mapping
 * columns onto item fields is the caller's job (it depends on which item
 * type the admin is bulk-importing as).
 */
export function parsePastedText(text) {
    const lines = text
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);

    if (lines.length === 0) return [];

    const delimiter = lines[0].includes('\t') ? '\t' : '|';

    return lines.map((line) => line.split(delimiter).map((cell) => cell.trim()));
}
