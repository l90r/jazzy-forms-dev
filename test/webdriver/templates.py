from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import NoSuchElementException
import unittest, time, re

class Templates(unittest.TestCase):
    def setUp(self):
        self.driver = webdriver.Firefox()
        self.driver.implicitly_wait(30)
        self.base_url = "http://blankito.local/workspace/wpbay/wp/"
        self.verificationErrors = []
        self.driver.get(self.base_url + "/templates")

    def input(self, input, radio):
        driver = self.driver
        driver.find_element_by_id("jzzf_18_a").clear()
        driver.find_element_by_id("jzzf_18_a").send_keys("%s\t" % input)
        driver.find_element_by_id("jzzf_18_b-%d" % radio).click()

    def test_basic(self):
        driver = self.driver
        self.input(1, 1)

        self.assertEqual("Result for 1", driver.find_element_by_id("jzzf_18_element").text)
        self.assertEqual("1*1=1", driver.find_element_by_id("jzzf_18_element_1").text)
        self.assertEqual("More info about 1", driver.find_element_by_id("jzzf_18_element_2").text)
        self.assertEqual("about 1", driver.find_element_by_link_text("about 1").text)

    def test_float(self):
        driver = self.driver
        self.input(1.1, 1)
                
        self.assertEqual("Result for 1.1", driver.find_element_by_id("jzzf_18_element").text)
        self.assertEqual("1.1*1=1.1", driver.find_element_by_id("jzzf_18_element_1").text)
        self.assertEqual("More info about 1.1", driver.find_element_by_id("jzzf_18_element_2").text)
        self.assertEqual("about 1.1", driver.find_element_by_link_text("about 1.1").text)

    def test_float2(self):
        driver = self.driver
        self.input(1.1, 2)

        self.assertEqual("Result for 1.1", driver.find_element_by_id("jzzf_18_element").text)
        self.assertEqual("1.1*2=2.2", driver.find_element_by_id("jzzf_18_element_1").text)
        self.assertEqual("More info about 2.2", driver.find_element_by_id("jzzf_18_element_2").text)
        self.assertEqual("about 2.2", driver.find_element_by_link_text("about 2.2").text)
        self.assertEqual("Leading Zeros Propagated: 0001.1", driver.find_element_by_id("jzzf_18_propagated").text)
        
    def tearDown(self):
        self.driver.quit()
        self.assertEqual([], self.verificationErrors)

if __name__ == "__main__":
    unittest.main()
