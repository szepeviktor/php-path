<?php

use Astrotomic\Path\PathString;
use Astrotomic\Path\Posix\Path as PosixPath;
use Astrotomic\Path\Win32\Path as WinPath;

test('can properly parse and then format paths with *nix', function(string $element, string $expectedRoot) {
    $output = PosixPath::parse($element);
    // Verify types of path object components.
    expect($output->root)
        ->toBeString()
        ->toBe($expectedRoot)
        ->and($output->dir)
        ->toBeString()
        ->toBe($output->dir ? PosixPath::dirname($element) : '')
        ->and($output->base)
        ->toBeString()
        ->toBe(PosixPath::basename($element))
        ->and($output->ext)
        ->toBeString()
        ->toBe(PosixPath::extname($element))
        ->and($output->name)
        ->toBeString()
        // Verify the object can be rendered back to the same string.
        ->and(PosixPath::format($output))
        ->toBeString()
        ->toBe($element);
})->with([
    // [path, root]
    ['/home/user/dir/file.txt', '/'],
    ['/home/user/a dir/another File.zip', '/'],
    ['/home/user/a dir//another&File.', '/'],
    ['/home/user/a$$$dir//another File.zip', '/'],
    ['user/dir/another File.zip', ''],
    ['file', ''],
    ['.\\file', ''],
    ['./file', ''],
    ['C:\\foo', ''],
    ['/', '/'],
    ['', ''],
    ['.', ''],
    ['..', ''],
    ['/foo', '/'],
    ['/foo.', '/'],
    ['/foo.bar', '/'],
    ['/.', '/'],
    ['/.foo', '/'],
    ['/.foo.bar', '/'],
    ['/foo/bar.baz', '/'],
]);

test('can properly parse and then format paths with Win32', function(string $element, string $expectedRoot) {
    $output = WinPath::parse($element);
    // Verify types of path object components.
    expect($output->root)
        ->toBeString()
        ->toBe($expectedRoot)
        ->and($output->dir)
        ->toBeString()
        ->toBe($output->dir ? WinPath::dirname($element) : '')
        ->and($output->base)
        ->toBeString()
        ->toBe(WinPath::basename($element))
        ->and($output->ext)
        ->toBeString()
        ->toBe(WinPath::extname($element))
        ->and($output->name)
        ->toBeString()
        // Verify the object can be rendered back to the same string.
        ->and(WinPath::format($output))
        ->toBeString()
        ->toBe($element);

})->with([
    // [path, root]
    ['C:\\path\\dir\\index.html', 'C:\\'],
    ['C:\\another_path\\DIR\\1\\2\\33\\\\index', 'C:\\'],
    ['another_path\\DIR with spaces\\1\\2\\33\\index', ''],
    ['\\', '\\'],
    ['\\foo\\C:', '\\'],
    ['file', ''],
    ['file:stream', ''],
    ['.\\file', ''],
    ['C:', 'C:'],
    ['C:.', 'C:'],
    ['C:..', 'C:'],
    ['C:abc', 'C:'],
    ['C:\\', 'C:\\'],
    ['C:\\abc', 'C:\\' ],
    ['', ''],

    // unc
    ['\\\\server\\share\\file_path', '\\\\server\\share\\'],
    ['\\\\server two\\shared folder\\file path.zip',
        '\\\\server two\\shared folder\\'],
    ['\\\\teela\\admin$\\system32', '\\\\teela\\admin$\\'],
    ['\\\\?\\UNC\\server\\share', '\\\\?\\UNC\\'],
]);

test('ensure special win32 paths parse', function(string $element, PathString $expected) {
    expect(WinPath::parse($element))
        ->toBeObject()
        ->toBe($expected);
})->with([
    ['t', new PathString(basename: 't', filename: 't')],
    ['/foo/bar', new PathString(directory: '/foo', root: '/', basename: 'bar', filename: 'bar')],
]);

test('ensure special win32 paths format', function(PathString $element, string $expected) {
    expect(WinPath::format($element))
        ->toBe($expected);
})->with([
    [new PathString(directory: 'some\\dir'), 'some\\dir\\'],
    [new PathString(basename: 'index.html'), 'index.html'],
    [new PathString(root: 'C:\\'), 'C:\\'],
    [new PathString(filename: 'index', extension: '.html'), 'index.html'],
    [new PathString(directory: 'some\\dir', filename: 'index', extension: '.html'), 'some\\dir\\index.html'],
    [new PathString(root: 'C:\\', filename: 'index', extension: '.html'), 'C:\\index.html'],
    [new PathString(), ''],
]);

test('ensure special *nix paths format', function(PathString $element, string $expected) {
    expect(PosixPath::format($element))
        ->toBe($expected);
})->with([
    [new PathString(directory: 'some/dir'), 'some/dir/'],
    [new PathString(basename: 'index.html'), 'index.html'],
    [new PathString(root: '/'), '/'],
    [new PathString(filename: 'index', extension: '.html'), 'index.html'],
    [new PathString(directory: 'some/dir', filename: 'index', extension: '.html'), 'some/dir/index.html'],
    [new PathString(root: '/', filename: 'index', extension: '.html'), '/index.html'],
    [new PathString(), ''],
]);