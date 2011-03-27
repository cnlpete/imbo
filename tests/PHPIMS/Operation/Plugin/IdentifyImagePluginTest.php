<?php
/**
 * PHPIMS
 *
 * Copyright (c) 2011 Christer Edvartsen <cogo@starzinger.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * * The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package PHPIMS
 * @subpackage Unittests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/phpims
 */

/**
 * @package PHPIMS
 * @subpackage Unittests
 * @author Christer Edvartsen <cogo@starzinger.net>
 * @copyright Copyright (c) 2011, Christer Edvartsen
 * @license http://www.opensource.org/licenses/mit-license MIT License
 * @link https://github.com/christeredvartsen/phpims
 */
class PHPIMS_Operation_Plugin_IdentifyImagePluginTest extends PHPUnit_Framework_TestCase {
    /**
     * Plugin instance
     *
     * @var PHPIMS_Operation_Plugin_IdentifyImagePlugin
     */
    protected $plugin = null;

    public function setUp() {
        $this->plugin = new PHPIMS_Operation_Plugin_IdentifyImagePlugin();
    }

    public function tearDown() {
        $this->plugin = null;
    }

    public function testSupportedMimeType() {
        $this->assertTrue(PHPIMS_Operation_Plugin_IdentifyImagePlugin::supportedMimeType('image/png'));
        $this->assertTrue(PHPIMS_Operation_Plugin_IdentifyImagePlugin::supportedMimeType('image/jpeg'));
        $this->assertTrue(PHPIMS_Operation_Plugin_IdentifyImagePlugin::supportedMimeType('image/gif'));
        $this->assertFalse(PHPIMS_Operation_Plugin_IdentifyImagePlugin::supportedMimeType('image/tiff'));
    }

    public function testGetFileExtension() {
        $this->assertSame('png', PHPIMS_Operation_Plugin_IdentifyImagePlugin::getFileExtension('image/png'));
        $this->assertSame('jpeg', PHPIMS_Operation_Plugin_IdentifyImagePlugin::getFileExtension('image/jpeg'));
        $this->assertSame('gif', PHPIMS_Operation_Plugin_IdentifyImagePlugin::getFileExtension('image/gif'));
        $this->assertFalse(PHPIMS_Operation_Plugin_IdentifyImagePlugin::getFileExtension('image/tiff'));
    }

    /**
     * @expectedException PHPIMS_Operation_Plugin_Exception
     * @expectedExceptionCode 415
     */
    public function testExecWithUnsupportedImageType() {
        $image = $this->getMock('PHPIMS_Image', array('getBlob'));
        $image->expects($this->once())
              ->method('getBlob')
              ->will($this->returnValue(file_get_contents(__FILE__)));

        $operation = $this->getMockBuilder('PHPIMS_Operation_AddImage')
                          ->setMethods(array('getImage'))
                          ->disableOriginalConstructor()
                          ->getMock();

        $operation->expects($this->once())
                  ->method('getImage')
                  ->will($this->returnValue($image));

        $this->plugin->setOperation($operation)
                     ->exec();
    }

    public function testExecWithValidImage() {
        $imageFile = __DIR__ . '/../../_files/image.png';

        $image = $this->getMock('PHPIMS_Image', array('getBlob', 'setMimeType', 'setExtension'));
        $image->expects($this->once())
              ->method('getBlob')
              ->will($this->returnValue(file_get_contents($imageFile)));

        $image->expects($this->once())
              ->method('setMimeType')->with('image/png')
              ->will($this->returnValue($image));

        $image->expects($this->once())
              ->method('setExtension')->with('png')
              ->will($this->returnValue($image));

        $operation = $this->getMockBuilder('PHPIMS_Operation_AddImage')
                          ->setMethods(array('getImage'))
                          ->disableOriginalConstructor()
                          ->getMock();

        $operation->expects($this->once())
                  ->method('getImage')
                  ->will($this->returnValue($image));

        $this->plugin->setOperation($operation)
                     ->exec();
    }
}